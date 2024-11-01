<?php
/*
Plugin Name: SVN Zip
Plugin URI: http://wordpress.org/extend/plugins/svnzip/
Author URI: http://flashpixx.de/2012/06/wordpress-plugin-svn-zip/
Description: The plugin creates a zip file for downloading a revision of a subversion
Author: flashpixx
Version: 0.1
 

#########################################################################
# GPL License                                                           #
#                                                                       #
# This file is part of the Wordpress SVNZip plugin.                     #
# Copyright (c) 2012, Philipp Kraus, <philipp.kraus@flashpixx.de>       #
# This program is free software: you can redistribute it and/or modify  #
# it under the terms of the GNU General Public License as published by  #
# the Free Software Foundation, either version 3 of the License, or     #
# (at your option) any later version.                                   #
#                                                                       #
# This program is distributed in the hope that it will be useful,       #
# but WITHOUT ANY WARRANTY; without even the implied warranty of        #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         #
# GNU General Public License for more details.                          #
#                                                                       #
# You should have received a copy of the GNU General Public License     #
# along with this program.  If not, see <http://www.gnu.org/licenses/>. #
#########################################################################
*/

namespace de\flashpixx\svnzip;
    
// ==== constant for developing with the correct path of the plugin ================================================================================
//define(__NAMESPACE__."\LOCALPLUGINFILE", __FILE__);
define(__NAMESPACE__."\LOCALPLUGINFILE", WP_PLUGIN_DIR."/svnzip/".basename(__FILE__));
// =================================================================================================================================================

    
    
// ==== plugin initialization ======================================================================================================================
// stop direct call
if (preg_match("#" . basename(__FILE__) . "#", $_SERVER["PHP_SELF"])) { die("You are not allowed to call this page directly."); }

// translation
if (function_exists("load_plugin_textdomain"))
	load_plugin_textdomain("svnzip", false, dirname(plugin_basename(LOCALPLUGINFILE))."/lang");
// =================================================================================================================================================  
    
    

// ==== create Wordpress Hooks =====================================================================================================================
add_filter("the_content", "de\\flashpixx\\svnzip\\filter");
add_action("init", "de\\flashpixx\\svnzip\\init");
register_activation_hook(LOCALPLUGINFILE, "de\\flashpixx\\svnzip\\install");
// =================================================================================================================================================

    

// ==== filter and other functions =================================================================================================================

/** installation function **/
function install()
{
    if (!extension_loaded("zip"))
        trigger_error(__("required zip extension not loaded", "svnzip"), E_USER_ERROR);
    if (!extension_loaded("svn"))
        trigger_error(__("required svn extension not loaded", "svnzip"), E_USER_ERROR);
}
    
    
/** we using sessions to communicate **/
function init()
{
    @session_start();
}


/** content filter function for get the tags
  * @param $pcContent Content
**/
function filter($pcContent)
{
	return preg_replace_callback("!\[svnzip(.*)\]!isU", "de\\flashpixx\\svnzip\\filteraction", $pcContent);
}


/** create action and the href tag
  * @param $pa Array with founded regular expressions
  * @return replace href tag or null on error
**/
function filteraction($pa)
{
	if ( (empty($pa)) || (count($pa) != 2) )
		    return null;

	// read tag parameter
	$param		= array();
	$tagparam   = $tags = preg_split('/\G(?:"[^"]*"|\'[^\']*\'|[^"\'\s]+)*\K\s+/', $pa[1], -1, PREG_SPLIT_NO_EMPTY);
	foreach($tagparam as $val)
	{
		// remove double / single quotes
		$lcTag = str_replace("\"", null, $val);
		$lcTag = str_replace("'", null, $lcTag);
		
		// find first occurence of = and split the string
		$laTag = preg_split('/=/', $lcTag, 2);
		if (count($laTag) == 2)
			$param[trim($laTag[0])] = trim($laTag[1]);
	}

	// if url-key not exists, abort
	if (!array_key_exists("url", $param))
		return "SVN Zip".__("URL parameter not exists", "svnzip");

	$lnRevision 	    = null;
	if (array_key_exists("revision", $param))
		$lnRevision = $param["revision"];
	
	$lcDownloadName	    = null;
	if (array_key_exists("downloadtext", $param))
		$lcDownloadName = $param["downloadtext"];
	
	$lcHash             = md5($param["url"].$lnRevision);
	$_SESSION[$lcHash] 	= array("url" => $param["url"], "revision" => $lnRevision, "downloadname" => $lcDownloadName);

	// create href tag
	$lcReturn  = "<a href=\"".plugins_url("download.php?h=".$lcHash, LOCALPLUGINFILE)."\"";
	if (array_key_exists("target", $param))
	    $lcReturn .= " target=\"".wp_specialchars($param["target"])."\"";
	if (array_key_exists("cssclass", $param))
		$lcReturn .= " class=\"".$param["cssclass"]."\"";
	$lcReturn .= ">";
	
	if (array_key_exists("linktext", $param))
		$lcReturn .= $param["linktext"];
	else
		$lcReturn .= __("SVN Download", "svnzip");
		
	return $lcReturn."</a>";
}

// =================================================================================================================================================

?>