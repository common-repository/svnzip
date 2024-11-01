<?php
/** 
 * #########################################################################
 * # GPL License                                                           #
 * #                                                                       #
 * # This file is part of the Wordpress SVNZip plugin.                     #
 * # Copyright (c) 2012, Philipp Kraus, <philipp.kraus@flashpixx.de>       #
 * # This program is free software: you can redistribute it and/or modify  #
 * # it under the terms of the GNU General Public License as published by  #
 * # the Free Software Foundation, either version 3 of the License, or     #
 * # (at your option) any later version.                                   #
 * #                                                                       #
 * # This program is distributed in the hope that it will be useful,       #
 * # but WITHOUT ANY WARRANTY; without even the implied warranty of        #
 * # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         #
 * # GNU General Public License for more details.                          #
 * #                                                                       #
 * # You should have received a copy of the GNU General Public License     #
 * # along with this program.  If not, see <http://www.gnu.org/licenses/>. #
 * #########################################################################
**/


// ==== read get parameter and decode =======================================
@session_start();

// hash not exists, then die
if (!isset($_GET["h"]))
	die("error");
$lcHash 	= $_GET["h"];

if (!isset($_SESSION[$lcHash]))
    die("error");

$svndata = $_SESSION[$lcHash];
if (!is_array($svndata))
	die("error");
if (!array_key_exists("url", $svndata))
	die("error");
if (!array_key_exists("revision", $svndata))
	die("error");
if (!array_key_exists("downloadname", $svndata))
	die("error");
// ==========================================================================


function addFiles2Zip($dir, $root, $zip)
{
	if ($handle = @opendir($dir)) {
		while (false !== ($file = readdir($handle))) { 
			if (($file == ".") ||( $file == ".."))
				continue;

			if (is_file($dir."/".$file)) {
				$lcFile = $dir."/".$file;
				$zip->addFile($lcFile, str_replace($root, null, $lcFile));
			}
			
			if ((is_dir($dir."/".$file)) && ($file !== ".svn"))
				addFiles2Zip($dir."/".$file, $root, $zip); 
		} 
		@closedir($handle); 
	} 
}


// create path and filenames
$lcZipName  	= tempnam(sys_get_temp_dir(), "svnzip_".$lcHash);
$lcSVNName  	= sys_get_temp_dir()."/svnzip_".$lcHash;
$lcDownloadName = empty($svndata["downloadname"]) ? "svndownload.zip" : $svndata["downloadname"].".zip";


// test if the directory exist, we do a svn update otherwise we do a checkout
if (is_dir($lcSVNName))
{
	if (svn_update($lcSVNName, $svndata["revision"]) === false)
		die("svn error");
} else {
	@mkdir($lcSVNName);
	if (!svn_checkout($svndata["url"], $lcSVNName, $svndata["revision"]))
		die("svn error");
}

$zip = new ZipArchive();
if ($zip->open($lcZipName, ZIPARCHIVE::CREATE) !== true)
	die("zip error");
addFiles2Zip($lcSVNName, $lcSVNName, $zip);
$zip->close();


if (!file_exists($lcZipName))
	die("zip file error");
	

# run download

header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: binary");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".$lcDownloadName."\"");
header("Content-Type: application/force-download");
header("Content-Type: application/download");

//IE specialize
header("Cache-Control: public, must-revalidate");
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: private");

header("Content-Length: ".filesize($lcZipName));
ob_clean();
flush();

$fd = @fopen($lcZipName, "rb");
while(!feof($fd))
    echo @fread($fd, 32768);
@fclose ($fd);

@unlink($lcZipName);
?>