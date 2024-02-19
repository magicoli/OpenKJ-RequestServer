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

if (isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'search') {
    $page = _( "Search result" );
    include("includes/search.php");
} else {
    $page = _( "Home" );
    $content = _( "Welcome to the karaoke, please use the search box to find a song to sing." );
}

pageheader( $page );

echo "<div class=container>";
echo "<header>";
navbar();
echo "</header>";
echo "<div id=main-content class=content>";

if( ! empty($notifications)) {
    if( ! empty( $notifications) ) {
        echo "<div class=notifications>";
        foreach ($notifications as $notice) {
            echo $notice;
        }
        echo "</div>";
    }
}

if( ! empty($content)) {
    echo sprintf(
        "<div class=content>%s</div>",
        $content,
    );
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
echo '<p class=info>Powered by 
<a href="http://openkj.org">OpenKJ</a> 
and <a href="https://magiiic.com/">Magiiic</a></p>';
echo "</footer>";

echo "</div>"; // end container

pagefooter();