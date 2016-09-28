<?php

namespace Neon\SlackPoints {

	class User {

		// ==========================================================
		// Save point
		// ==========================================================

		public static function save_point($data) {

			// DB object
			$db = new \Neon\Database();

			// Get data
			$tables = $db->get_data("CALL Users_SavePoint(
				:slack_team_id, 
				:slack_channel_id, 
				:slack_channel_name, 
				:slack_creating_user_id, 
				:slack_creating_user_name, 
				:slack_target_user_name, 
				:slack_command_text,
				:private
			)", 
				array(
					'slack_team_id' => $data['team_id'],
					'slack_channel_id' => $data['channel_id'],
					'slack_channel_name' => $data['channel_name'],
					'slack_creating_user_id' => $data['user_id'],
					'slack_creating_user_name' => $data['user_name'],
					'slack_target_user_name' => $data['target_user_name'],
					'slack_command_text' => $data['text'],
					'private' => $data['private']
				)
			);

			// ------------------------------------------------------
			// Return
			// ------------------------------------------------------
			if (count($tables) === 0 || count($tables[0]) === 0) return -1;
			return $tables;
		}

		// ==========================================================
		// Get token for team
		// ==========================================================

		public static function get_token_for_team($team_id) {

			// DB object
			$db = new \Neon\Database();

			// Get data
			$tables = $db->get_data("CALL Teams_GetTokenByTeamID(:team_id)", 
				array(
					'team_id' => $team_id
				)
			);

			// ------------------------------------------------------
			// Return
			// ------------------------------------------------------
			if (count($tables) === 0 || count($tables[0]) === 0) return -1;
			return $tables[0][0]['token'];
		}

		// ==========================================================
		// Get leaderboard for team
		// ==========================================================

		public static function get_leaderboard_for_team($team_id, $start_date = null) {

			// DB object
			$db = new \Neon\Database();

			// Get data
			$tables = $db->get_data("CALL Teams_GetLeaderboard(:team_id, :start_date)", 
				array(
					'team_id' => $team_id,
					'start_date' => $start_date
				)
			);

			// ------------------------------------------------------
			// Return
			// ------------------------------------------------------
			if (count($tables) === 0 || count($tables[0]) === 0) return false;
			return $tables;
		}

		// ==========================================================
		// Get team members
		// ==========================================================

		public static function get_team_members($team_id) {

			// Get token
			$token = \Neon\SlackPoints\User::get_token_for_team($team_id);
			if (!$token) return [];

			// Get team members
			return json_decode(file_get_contents("https://slack.com/api/users.list?token=$token"));

		}

		// ==========================================================
		// Check user name
		// ==========================================================

		public static function check_user_name($user_name, $team_id) {

			// Get team members
			$team_members = \Neon\SlackPoints\User::get_team_members($team_id);
			if (!$team_members) return ['error' => 'No response from Slack API', 'code' => -1];
			if (isset($team_members->error)) {
				return ['error' => $team_members->error, 'code' => -2];
			}

			// Loop
			foreach ($team_members->members as $team_member) {
				if ($team_member->name == $user_name) return ['error' => null, 'code' => 1];
			}

			// Return
			return ['error' => 'Username not found...', 'code' => -3];

		}

	}

}

?>