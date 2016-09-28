<?php

// Config
require_once('../config.php');

// Parameters
$parameters = ['team_id', 'channel_name', 'user_name'];

// Response
$response = 'No data. Yet.';

// Data
$data = [];
foreach ($parameters as $parameter) {
	$value = \Neon\Common::REQUEST($parameter);
	if (!isset($value)) {
		http_response_code(400);
		echo 'missing vars';
		exit;
	}
	$data[$parameter] = $value;
}

// Check team
if (!in_array(\Neon\Common::REQUEST('team_id'), $allowed_team_ids)) {
	echo 'Team not authorised.';
	exit;
}

// Public/private notification
$text = \Neon\Common::REQUEST('text');
$data['private'] = isset($text) || preg_match('/ private/', $text) ? true : false;

// Day
$day_start =  gmdate('Y-m-d');
$week_start = date('Y-m-d G:i', strtotime('last monday', strtotime('tomorrow')));
$month_start = gmdate('Y-m-01');
$year_start = gmdate('Y-01-01');
$start = \Neon\Common::REQUEST('text');
$start_date = null;
if ($start) {
	switch ($start) {
		case 'today':
			$start_date = $day_start;
			break;
		case 'week':
			$start_date = $week_start;
			break;
		case 'month':
			$start_date = $month_start;
			break;
		case 'year':
			$start_date = $year_start;
			break;
		default:
			$start = null;
			break;
	}
}
if (!$start) $start = 'all time';

// Save
$result = \Neon\SlackPoints\User::get_leaderboard_for_team($data['team_id'], $start_date);
if ($result && count($result) > 1) {

	// Create response
	if (count($result[1]) > 0) {
		$response = 'Leaderboard (' . ($start == 'all time' ? $start : 'this ' . $start) . ')';
		foreach ($result[1] as $entry) {
			$response .= PHP_EOL . '@'.$entry['user_name'] . ': ' . $entry['total'];
		}
		if (!$data['private']) $response .= PHP_EOL . '(posted by @' . $data['user_name'] . ')';
	}
	
	// Get vars
	$slackbot_token = count($result[0]) > 0 ? $result[0][0]['slackbot_token'] : false;
	$team_short_name = count($result[0]) > 0 ? $result[0][0]['short_name'] : false;

	// Private
	if ($data['private'] || !$slackbot_token || !$team_short_name || $data['channel_name'] == 'privategroup') {

		// Attempting to post a public notification to a private group
		if ($data['channel_name'] == 'privategroup' && !$data['private']) $response .= PHP_EOL . ' FYI I can\'t post a public message in a private group :)';

		// Output
		echo $response;
	}

	// Public
	else {

		// Channel name
		$channel_name = $data['channel_name'];

		// URL
		$url = "https://$team_short_name.slack.com/services/hooks/slackbot?token=$slackbot_token&channel=%23$channel_name";

		// Create CURL request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,           $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $response ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
		$result = curl_exec($ch);

	}

}

