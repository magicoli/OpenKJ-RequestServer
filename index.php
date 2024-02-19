<?php
/**
 * OpenKJ
 * 
 * This is the main page for the OpenKJ application. It is the first page that is loaded when the user visits the site.
 * 
 * @package openkj
 * @version 2.0.0
 * @since 1.0.0
 */

define('IN_OPENKJ', true);
require_once("includes/global.php");

pageheader("Home");

echo "<div class=container>";
echo "<header>";
navbar();
echo "</header>";
echo "<div class=content>";
echo _("Hello, world!");

if (isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'search') {
    include("includes/search.php");
}

/*
if ($screensize == 'xlarge')
{
	echo "<br><br><p class=info>Want to search using your smartphone, tablet, or laptop?<br><br>Browse to songbook.openkj.org/venue/$url_name in the web browser on your device.<br><br>
";
}
*/
echo "</div>"; // end content

echo "<footer>";
echo "<p class=info>Powered by OpenKJ and Magiiic</p>";
echo "</footer>";

echo "</div>"; // end container

pagefooter();