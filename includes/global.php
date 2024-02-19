<?php
/**
 * OpenKJ Standalone Songbook
 * 
 * This is a standalone version of the OpenKJ Songbook.  It is designed to be used with the OpenKJ standalone server.
 * 
 * This file contains the global functions and settings for the standalone songbook.
**/

defined('IN_OPENKJ') or die('No direct script access.');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
define( "BASE_URL", $base_url );
define( "BASE_DIR", dirname( __DIR__ ) );

if( ! file_exists( BASE_DIR . "/config/settings.php" ) )
{
	echo "Please copy config/example.settings.php to config/settings.php and edit it to configure your venue";
	die();
}

require_once( BASE_DIR . "/config/settings.php" );
require_once( BASE_DIR . "/includes/translations.php" );

$db = new PDO("sqlite:$dbFilePath");

$db->exec("CREATE TABLE IF NOT EXISTS songdb (song_id integer PRIMARY KEY AUTOINCREMENT, artist text, title  TEXT, combined TEXT UNIQUE)"); 
$db->exec("CREATE TABLE IF NOT EXISTS state (accepting bool, serial integer NOT NULL)");
$db->exec("INSERT OR IGNORE INTO state (rowid,accepting,serial) VALUES(0,0,1)");
$db->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_songstrings ON songdb(combined)");
$db->exec("CREATE TABLE IF NOT EXISTS requests (request_id integer PRIMARY KEY AUTOINCREMENT, artist TEXT, title TEXT, singer TEXT, request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$songdbtable = "songdb";
$requeststable = "requests";
if (isset($_SERVER['REFERER'])) $referer = $_SERVER['REFERER'];

function addcss( $styles) {
	global $theme;
	if( ! is_array( $styles ) ) $styles = array( $styles );
	foreach ($styles as $css) {
		if(file_exists("$css")) {
			printf(
				'<link rel=stylesheet type="text/css" href="%s" />', 
				$css . '?v=' . filemtime($css),
			);
		}
	}
}

function addjs( $scripts) {
	if( ! is_array( $scripts ) ) $scripts = array( $scripts );
	foreach ($scripts as $js) {
		if(file_exists("$js")) {
			printf(
				'<script type="text/javascript" src="%s"></script>', 
				$js . '?v=' . filemtime($js),
			);
		}
	}
}

function pageheader($title) 
{
	global $venueName;
	global $screensize;
	global $theme;
	echo "<html><head>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>$venueName - OpenKJ Karaoke Songbook</title>
	<link href='https://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet'>
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>";
	addcss( array( "css/main.css", "themes/$theme.css", "config/custom.css" ) );
	addjs( array( "js/main.js" ) );
	echo "</head><body>";
}

function pagefooter() {
	echo "</body></html>";
}

function navbar($backurl = "", $echo = true) 
{
	global $venueName;
	global $screensize;
	if ( empty($backurl) || $backurl == "index.php" ) $backurl = dirname( getenv( "REQUEST_URI" ) );
	$nav = sprintf(
		'<nav id=navbar class=navbar>
			<a href="%s" class=home-link>
				<h1 class=site-title>%s</h1>
			</a>
			%s
		</nav>',
		BASE_URL,
		( empty($venueName ) ? "OpenKJ Songbook" : $venueName ),
		searchform(false),
	);
	if( $echo ) echo $nav; else return $nav;
}

// TODO: seems to belong to api
function setAccepting($accepting)
{
	global $db;
	if ($accepting == 1)
	{
		echo("setting accepting to 1");
		$db->exec("UPDATE state SET accepting=1");
	}
	else
	{
		echo("setting accepting to 0");
		$db->exec("UPDATE state SET accepting=0");
	}
}

function getAccepting()
{
	global $db;
	$accepting = false;
        foreach ($db->query("SELECT accepting FROM state LIMIT 1") as $row)
        {
                $accepting = $row['accepting'];
        }
	return $accepting;
}

function add_notice($message, $status = "info")
{
	global $notifications;

	if(empty($message)) return;

	$notifications[] = sprintf (
		'<div class="notice notice-%s">%s</div>',
		$status,
		$message,
	);
}

function searchform( $echo = true ) 
{
	global $db;
	global $venue_id;
	global $search_summary;
	if (!getAccepting())
	{
		add_notice ( _( "Heads up, we're not taking requests at the moment." ), "error" );
	}
	else
	{
		global $url_name;
		global $screensize;
		$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
		$html = sprintf(
			'<div class=search-form>
			<form method="get" action="%s">
				<label class=search-summary>%s</label>
				<input type=hidden name=action value="search">
				<input type=text name=q id=q placeholder="%s" value="%s" autofocus autocomplete=off>
				<input type=submit value=%s>
			</form>
			</div>',
			htmlspecialchars(BASE_URL),
			$search_summary,
			htmlspecialchars(_("Song or artist")),
			htmlspecialchars($q),
			htmlspecialchars(_("Search")),
		);
	}
	if( $echo ) echo $html; else return $html;
}

function getSerial()
{
	global $db;
	$serial = -1;
	foreach ($db->query("SELECT serial FROM state LIMIT 1") as $row)
        {
        	$serial = (int)$row['serial'];
        }
	return $serial;
}

function newSerial()
{
        global $db;
        $serial = getSerial();
        $newSerial = mt_rand(0,99999);
        while ($newSerial == $serial)
        {
                $newSerial = mt_rand(0,99999);
        }
        $db->exec("UPDATE state SET serial=$newSerial");
        return $newSerial;
}

function getVenue()
{
	// We don't really do multiple venues in standalone, just fake it
        global $db;
	global $venueName;
        $serial = -1;
	$venue = array();
	$venue['venue_id'] = $venue_id;
	$venue['accepting'] = getAccepting();
	$venue['name'] = $venueName;
	$venue['url_name'] = "none";
	return $venue;
}

function getVenues()
{
	// We don't really do multiple venues in standalone, just fake it
	global $db;
	global $venueName;
	$venues = array();
	$venue['venue_id'] = 0;
        $venue['accepting'] = getAccepting();
        $venue['name'] = $venueName;
	$venue['url_name'] = "none";
	$venues['venues'][] = $venue;
	return $venues;
}

function getRequests()
{
        global $db;
        $requests = array();
        $result = $db->query("SELECT request_id,artist,title,singer,strftime('%s', request_time) AS unixtime FROM requests");
	if ($result)
	{
        	foreach ($result as $row)
        	{
        	        $request['request_id'] = (int)$row['request_id'];
        	        $request['artist'] = $row['artist'];
        	        $request['title'] = $row['title'];
        	        $request['singer'] = $row['singer'];
        	        $request['request_time'] = (int)$row['unixtime'];
        	        $requests['requests'][] = $request;
        	}
	}
        return $requests;
}


?>
