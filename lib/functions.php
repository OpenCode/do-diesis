<?php

	function date_to_datetime($date) {
		$array = explode("/", $date);
		$datetime = $array[2] . '-' . $array[1] . '-' . $array[0];
		return $datetime;
		}

	function datetime_to_date($datetime) {
		$array = explode("-", $datetime);
		$date = $array[2] . '/' . $array[1] . '/' . $array[0];
		return $date;
		}
		
	function extract_relation_id($complete_name) {
		preg_match("#\[(.+?)\]#m",$complete_name,$group_match);
		if (!$group_match) {
			return null;
			}
		return $group_match[1];
		}

?>
