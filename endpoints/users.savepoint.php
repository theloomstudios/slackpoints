<?php

// Config
require_once('../config.php');

// Parameters
$parameters = ['team_id', 'channel_id', 'channel_name', 'user_id', 'user_name', 'command', 'text'];

// Data
$data = [];
foreach ($parameters as $parameter) {
	$value = \Neon\Common::REQUEST($parameter);
	if (!isset($value)) {
		http_response_code(400);
		echo 'Um... did you forget the username?';
		exit;
	}
	$data[$parameter] = $value;
}

// Check team
if (!in_array(\Neon\Common::REQUEST('team_id'), $allowed_team_ids)) {
	echo 'Team not authorised.';
	exit;
}

// Check username
// $user_name_check = \Neon\SlackPoints\User::check_user_name(\Neon\Common::REQUEST('user_name'), \Neon\Common::REQUEST('team_id'));
// if ($user_name_check['code'] < 0) {
// 	echo $user_name_check['error'];
// 	exit;
// }

function post_to_channel($team_short_name, $channel_name, $slackbot_token, $message) {

	if ($team_short_name && $channel_name && $slackbot_token && $message) {

		// URL
		$url = "https://$team_short_name.slack.com/services/hooks/slackbot?token=$slackbot_token&channel=%23$channel_name";

		// Create CURL request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,           $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $message ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
		$result = curl_exec($ch);
	}

}

// Target user name
preg_match_all('/@([\S]+)/', $data['text'], $matches);

// Public/private notification
$data['private'] = preg_match('/@[\S]+ private/', $data['text']) ? true : false;

// Proceed
if (count($matches[1]) === 1) {

	// Set target user name
	$data['target_user_name'] = $matches[1][0];

	// Save
	$result = \Neon\SlackPoints\User::save_point($data);

	// Bad result
	if (!$result) {
		echo 'Unknown result from save.';
		exit;
	}

	// Proceed
	else {

		// Set vars
		$status_id = $result[0][0]['status_id'];
		$time_until_next_point = $result[0][0]['time_until_next_point'];
		$total_points_for_user = $result[0][0]['total_points_for_user'];
		$slackbot_token = count($result) > 1 && count($result[1]) > 0 ? $result[1][0]['slackbot_token'] : false;
		$team_short_name = count($result) > 1 && count($result[1]) > 0 ? $result[1][0]['short_name'] : false;

		// Check status_id
		if ($status_id === '1') {

			// Message
			$message = '+1 for @'.$data['target_user_name'].' from @' . $data['user_name'] . '!';

			// Private or team API credentials missing
			if ($data['private'] || !$slackbot_token || !$team_short_name || $data['channel_name'] == 'privategroup') {

				// Attempting to post a public notification to a private group
				if ($data['channel_name'] == 'privategroup' && !$data['private']) $message .= ' FYI I can\'t post a public message in a private group :)';

				// Output
				echo $message;
			}
			
			// Public
			else {

				// Post public notification
				post_to_channel($team_short_name, $data['channel_name'], $slackbot_token, $message);

			}

			// Target user has multiple of 10 points
			if ($total_points_for_user % 10 === 0) {

				// Message
				$progress_message = '@' . $data['target_user_name'] . ' just reached ' . $total_points_for_user . ' points!';

				// Post to channel
				post_to_channel($team_short_name, $data['channel_name'], $slackbot_token, $progress_message);

			}

		}
		else if ($status_id === '2') {
			echo 'Nice try. Conceited much?';
		}
		else if ($status_id === '3') {
			$plural = $time_until_next_point == 1 ? '' : 's';
			// echo "Hold your horses, you'll be able to assign another point in $time_until_next_point minute$plural.";
			echo "Woah slow down! You'll be able to dish out more points soon.";
		}
		else {
			echo 'Unknown response from save.';
		}
	}

}
else {
	if (count($matches[1]) === 0) {
		echo 'Um... did you forget the username?';
	}
	else {
		echo 'Woah! One username at a time.';
	}
}
