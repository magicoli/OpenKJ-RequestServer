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

siteheader("Home");
navbar();

echo _("Hello, world!");

searchform();
/*
if ($screensize == 'xlarge')
{
	echo "<br><br><p class=info>Want to search using your smartphone, tablet, or laptop?<br><br>Browse to songbook.openkj.org/venue/$url_name in the web browser on your device.<br><br>
";
}
*/
sitefooter();
