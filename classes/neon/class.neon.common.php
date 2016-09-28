<?php

namespace Neon {

	class Common {

		public static function GET($key) {
			if (isset($_GET[$key])) return $_GET[$key];
			return null;
		}

		public static function REQUEST($key, $parseAsBit = false) {

			if (!empty($_REQUEST[$key])) {

				if ($parseAsBit) {
					return $_REQUEST[$key] == 'true' ? 1 : 0;
				}

				return $_REQUEST[$key];
			}
			return null;
		}

		public static function NullifyEmptyObject($object) {
			if (empty($object) && $object !== 0) return null;
			return $object;
		}

		public static function GetUTCOffsetForTimezone($timezone) {
			$dtz = new DateTimeZone($timezone);
			$time_in_timezone = new DateTime('now', $dtz);
			return $dtz->getOffset($time_in_timezone) / 60 / 60;
		}

		public static function ConvertUTCToLocal($utc) {
			$local_date = \DateTime::createFromFormat('Y-m-d H:i:s', $utc, new \DateTimeZone('UTC'));
			$local_date->setTimeZone(new \DateTimeZone(TIMEZONE));
			return $local_date->format('Y-m-d H:i:s');
		}

		public static function ParseHashtags($string) {
			$pattern = '/#([a-z0-9]+)/i';
			$replacement = '<span class="highlight">#$1</a>';
			return preg_replace($pattern, $replacement, $string);
		}

		public static function ParseInstagramUsernames($string) {
			$pattern = '/@([a-z0-9]+)/i';
			$replacement = '<a href="https://instagram.com/$1/" target="_blank" class="highlight">@$1</a>';
			return preg_replace($pattern, $replacement, $string);
		}

		public static function ParseLinks($string) {
			$pattern = '/(https?:\/\/[a-z0-9\.\/]+)/i';
			$replacement = '<a href="$1" target="_blank" class="highlight">$1</a>';
			return preg_replace($pattern, $replacement, $string);
		}

		public static function Redirect($url) {
			header("Location: " . $url);	
		}
		
		public static function FormatDate($date, $format) {
			if (intval($date) == 0) return null;
			return date($format, strtotime($date));
		}
		
		public static function GetSelected($value1, $value2) {
			if ($value1 == $value2) return "selected=\"selected\"";
			return null;
		}

		public static function GetChecked($value, $defaultToTrue = false) {
			if ($value == true || ($value == null && $defaultToTrue)) return "checked=\"checked\"";
			return null;
		}
		
		/*public static function TimeDifference($date_stop, $date_start) {
			$seconds = strtotime($date_stop) - strtotime($date_start);
			$minutes = $seconds / 60;
			$remaining_minutes = $minutes % 60;
			$hours = floor($minutes / 60);
			$string = '';
			if ($hours > 0) $string .= $hours.'h';
			if ($remaining_minutes > 0) $string .= $remaining_minutes.'m';
			return $string;
		}*/

		public static function TimeDifference($date_stop, $date_start) {
			$seconds = strtotime($date_stop) - strtotime($date_start);
			$minutes = $seconds / 60;
			$remaining_minutes = $minutes % 60;
			$hours = $minutes / 60;
			$string = '';
			if ($hours >= 1) {
				$string = round($hours, 1) . 'h';
			}
			else {
				$string .= $remaining_minutes.'m';
			}
			
			return $string;
		}

		public static function ParseTime($_minutes) {
			$hours = floor($_minutes / 60);
			$minutes = $_minutes % 60;
			$parsed = $minutes . "m";
			if ($hours > 0) $parsed = $hours . "h " . $parsed;
			return $parsed;
		}
		
		public static function ParseHours($_minutes) {
			return(number_format($_minutes / 60, 1));
		}

		public static function StartSession() {
			if(session_id() == '') {
				session_start();
			}	
		}
		
		public static function Summary($text, $limit) {
			if (strlen($text) <= $limit) return $text;
			return trim(substr($text, 0, $limit)) . '...';	
		}
		
		public static function GetPlural($total, $singular, $plural) {
			if ($total == 1) return $singular;
			return $plural;
		}

		public static function HumanTime($date) {

			// Time elapsed
		    $time = time() - strtotime($date);

		    // Units
		    $units = array (
		        31536000 => 'year',
		        2592000 => 'month',
		        604800 => 'week',
		        86400 => 'day',
		        3600 => 'hour',
		        60 => 'minute',
		        1 => 'second'
		    );

		    // Loop
		    foreach ($units as $unit => $text) {
		        if ($time < $unit) continue;
		        $numberOfUnits = floor($time / $unit);
		        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
		    }

		}

		public static function ConvertHexToRGB($hex) {

			$hex = str_replace("#", "", $hex);

			if (strlen($hex) == 3) {
				$r = hexdec(substr($hex,0,1).substr($hex,0,1));
				$g = hexdec(substr($hex,1,1).substr($hex,1,1));
				$b = hexdec(substr($hex,2,1).substr($hex,2,1));
			} 
			else {
				$r = hexdec(substr($hex,0,2));
				$g = hexdec(substr($hex,2,2));
				$b = hexdec(substr($hex,4,2));
			}

			$rgb = array($r, $g, $b);
			return implode(",", $rgb);
		}

	}

}

?>