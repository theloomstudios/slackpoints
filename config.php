<?php

// Start session
// session_start();

// Server profiles
$server_profiles = array(

	// Staging
	'staging' => array(
		'site_url' => '', // required
		'document_root' => '', // required
		'database' => array(
			'host' => 'localhost', // required
			'user' => '', // required
			'password' => '', // required
			'database' => '' // required
		)
	),

	// Production
	'production' => array(
		'site_url' => '', // required
		'document_root' => '', // required
		'database' => array(
			'host' => 'localhost', // required
			'user' => '', // required
			'password' => '', // required
			'database' => '' // required
		)
	)

);

// Set server profile
$server_profile = $server_profiles['production'];
error_reporting(0);
if (strpos($_SERVER['HTTP_HOST'], 'staging') !== FALSE) {
	$server_profile = $server_profiles['staging'];
	error_reporting(E_ALL);
}

// Constants
define("DB_DEBUG_OUTPUT", false);
define("DB_DEBUG_DATABASE", true);
define("TIMEZONE", "Australia/Sydney");
define("SITE_URL", $server_profile['site_url']);
define("DOCUMENT_ROOT", $server_profile['document_root']);
define("DB_HOST", $server_profile['database']['host']);
define("DB_USER", $server_profile['database']['user']);
define("DB_PASSWORD", $server_profile['database']['password']);
define("DB_DATABASE", $server_profile['database']['database']);

// Allowed teams
$allowed_team_ids = array(''); // required

// Set timezone
date_default_timezone_set(TIMEZONE);

// NR Classes
require_once DOCUMENT_ROOT.'classes/neon/class.neon.common.php';
require_once DOCUMENT_ROOT.'classes/neon/class.neon.database.php';
require_once DOCUMENT_ROOT.'classes/neon/class.neon.debug.php';

// Slackpoints Classes
require_once DOCUMENT_ROOT.'classes/neon/class.neon.slackpoints.user.php';

?>