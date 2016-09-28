<?php

namespace Neon {

	class Debug {

		public static function get_data() {

			// DB object
			$db = new \Neon\Database();

			// Get data
			$results = $db->get_data("CALL Debug_GetData()", 
				array(
					
				),
				false
			);

			return $results;

		}

		// ==========================================================
		// Save
		// ==========================================================

		public static function save($data) {

			// DB object
			$db = new \Neon\Database();

			// Get data
			$tables = $db->get_data("CALL Debug_Save(:data)", 
				array(
					'data' => $data
				),
				false
			);

		}

	}

}

?>