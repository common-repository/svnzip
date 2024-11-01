=== Plugin Name ===
Contributors: flashpixx
Tags: svn, subversion, download, zip, revision, repository
Requires at least: 2.7
Tested up to: 3.4.2
Stable tag: 0.1
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WCRMFYTNCJRAU
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html


With this plugin a zip download link of a subversion repository can be created within blog articles and pages 


== Description ==

<strong>This plugin is not be in developing anymore, all functionality is moved to a new plugin <a href="http://wordpress.org/extend/plugins/repositoryzip/">Repository Zip</a></strong>

The plugin creates zip download links within articles and pages of a subversion repository. On each call the subversion revision number, link text, css name and download
name can be set, so that each link points to different subversion. The plugin need no configuration or something else.


== Installation ==

1.  Upload the folder to the "/wp-content/plugins/" directory
2.  Activate the plugin through the 'Plugins' menu in WordPress


== Requirements ==

* Wordpress 3.2 or newer
* PHP 5.3.0 or newer 
* <a href="http://de3.php.net/manual/en/book.svn.php">PHP SVN extension</a>
* <a href="http://de3.php.net/manual/en/book.zip.php">PHP Zip extension</a>


== Shortcode ==

Add to your post or page content only
<pre>[svnzip url="url-to-your-svn"]</pre>
The shortcut allows additional parameter:
<ul>
<li>"revision" defines a special revision, which is used. If the parameter is not set, the latest revision is used</li>
<li>"downloadname" setup a filename, which is shown in the browser</li>
<li>"target" defines the target parameter of the link</li>
<li>"cssclass" sets the CSS class of the link</li>
<li>"linktext" sets the text of the link</li>
</ul>


== Frequently Asked Questions ==

= Can I change the download name or add additional options ? =
Yes you can do this, the plugin tag allows some option values <pre>[svnzip url="url-to-your-svn" revision="your-revision" downloadname="name-of-the-download-file-without-extension" target="target-parameter-of-the-href-tag" cssclass="css-class-of-the-href-tag" linktext="text-of-the-href-tag"]</pre>


== Upgrade Notice ==

= 0.2 =
On this version the underlaying object-orientated structure of the plugin uses the PHP namespaces, which added in the PHP version
5.3.0. So the plugin needs a PHP version equal or newer than PHP 5.3.0



== Changelog == 

= 0.2 =

* add PHP namespaces (needs not PHP 5.3 or greater)
* change language domain to "svnzip"

= 0.1 =

* first version with the base functions