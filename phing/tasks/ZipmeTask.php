<?php
/**
 * Akeeba Build Files
 *
 * @package    buildfiles
 * @copyright  (c) 2010-2017 Akeeba Ltd
 */

//require_once "phing/Task.php";
//require_once 'phing/tasks/system/MatchingTask.php';
//include_once 'phing/util/SourceFileScanner.php';
//include_once 'phing/mappers/MergeMapper.php';
//include_once 'phing/util/StringHelper.php';

require_once __DIR__ . '/library/ZipmeFileSet.php';

/**
 * Creates a ZIP archive
 */
class ZipmeTask extends MatchingTask
{
	/**
	 * The output file
	 *
	 * @var   PhingFile
	 */
	private $zipFile;

	/**
	 * The directory that holds the data to include in the archive
	 *
	 * @var   PhingFile
	 */
	private $baseDir;

	/**
	 * File path prefix in ZIP archive
	 *
	 * @var   string
	 */
	private $prefix = null;

	/**
	 * Should I include empty dirs in the archive.
	 *
	 * @var   bool
	 */
	private $includeEmpty = true;

	/**
	 * The filesets to include to the archive
	 *
	 * @var   array
	 */
	private $filesets = array();

	/**
	 * Add a new fileset.
	 *
	 * @return  FileSet
	 */
	public function createFileSet()
	{
		$this->fileset = new ZipmeFileSet();
		$this->filesets[] = $this->fileset;

		return $this->fileset;
	}

	/**
	 * Add a new fileset.
	 *
	 * @return  FileSet
	 */
	public function createZipmeFileSet()
	{
		$this->fileset = new ZipmeFileSet();
		$this->filesets[] = $this->fileset;

		return $this->fileset;
	}

	/**
	 * Set the name/location of where to create the JPA file.
	 *
	 * @param   PhingFile  $destFile  The location of the output JPA file
	 */
	public function setDestFile(PhingFile $destFile)
	{
		$this->zipFile = $destFile;
	}

	/**
	 * Set the include empty directories flag.
	 *
	 * @param   boolean  $bool  Should empty directories be included in the archive?
	 *
	 * @return  void
	 */
	public function setIncludeEmptyDirs($bool)
	{
		$this->includeEmpty = (boolean)$bool;
	}

	/**
	 * This is the base directory to look in for files to archive.
	 *
	 * @param   PhingFile  $baseDir  The base directory to scan
	 *
	 * @return  void
	 */
	public function setBasedir(PhingFile $baseDir)
	{
		$this->baseDir = $baseDir;
	}

	/**
	 * Sets the file path prefix for files in the JPA archive
	 *
	 * @param   string  $prefix  Prefix
	 *
	 * @return  void
	 */
	public function setPrefix(string $prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * Do the work
	 *
	 * @throws BuildException
	 */
	public function main()
	{
		if ($this->zipFile === null)
		{
			throw new BuildException("zipFile attribute must be set!", $this->getLocation());
		}

		if ($this->zipFile->exists() && $this->zipFile->isDirectory())
		{
			throw new BuildException("zipFile is a directory!", $this->getLocation());
		}

		if ($this->zipFile->exists() && !$this->zipFile->canWrite())
		{
			throw new BuildException("Can not write to the specified zipFile!", $this->getLocation());
		}

		$savedFileSets = $this->filesets;

		try
		{
			if (empty($this->filesets))
			{
				throw new BuildException("You must supply some nested filesets.", $this->getLocation());
			}

			$this->log("Building ZIP: " . $this->zipFile->__toString(), Project::MSG_INFO);

			$absolutePath = $this->zipFile->getAbsolutePath();

			if (!is_dir(dirname($absolutePath)))
			{
				throw new BuildException("ZIP file path $absolutePath is not a path.", $this->getLocation());
			}

			$zip = new ZipArchive();
			$res = $zip->open($absolutePath, ZipArchive::CREATE);

			if ($res !== true)
			{
				throw new Exception("ZipArchive::open() failed, code $res");
			}

			foreach ($this->filesets as $fs)
			{
				$files     = $fs->getFiles($this->project, $this->includeEmpty);
				$fsBasedir = (null != $this->baseDir) ? $this->baseDir : $fs->getDir($this->project);

				foreach ($files as $file)
				{
					$f = new PhingFile($fsBasedir, $file);

					$pathInZIP = $this->prefix . $f->getPathWithoutBase($fsBasedir);
					$zip->addFile($f->getPath(), $pathInZIP);
					$this->log("Adding " . $f->getPath() . " as " . $pathInZIP . " to archive.", Project::MSG_VERBOSE);
				}
			}
		}
		catch (IOException $ioe)
		{
			$msg            = "Problem creating ZIP: " . $ioe->getMessage();
			$this->filesets = $savedFileSets;

			throw new BuildException($msg, $ioe, $this->getLocation());
		}

		$zip->setArchiveComment("Created with Akeeba Build Files - https://github.com/akeeba/buildfiles");
		$zip->close();

		$this->filesets = $savedFileSets;
	}
}