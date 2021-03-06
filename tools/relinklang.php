<?php
/**
 * Akeeba Build Tools
 *
 * @package    buildfiles
 * @license    GNU/GPL v3
 * @copyright  Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 */

/**
 * Language file relinker (obsolete)
 */

function showUsage()
{
	$file = basename(__FILE__);
	echo <<<ENDUSAGE

Usage:
	php $file /path/to/site /path/to/repository

ENDUSAGE;
}

$year = gmdate('Y');
echo <<<ENDBANNER
Akeeba Build Tools - Language Relinker
No-configuration extension translation linker
-------------------------------------------------------------------------------
Copyright ©2010-$year Nicholas K. Dionysopoulos / AkeebaBackup.com
Distributed under the GNU General Public License v3 or later
-------------------------------------------------------------------------------

!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!!!!!!!!!!!!!!!!!!!!!!!!!!!!! THIS TOOL IS OBSOLETE !!!!!!!!!!!!!!!!!!!!!!!!!!!!
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

This tool is obsolete as of March 2016. Language relinking is now part of the
relink.php utility.

ENDBANNER;
