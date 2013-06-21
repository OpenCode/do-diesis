<?php

	// Change date from DD/MM/YYYY to YYYY-MM-DD
	function date_to_datetime($date) {
		$array = explode("/", $date);
		$datetime = $array[2] . '-' . $array[1] . '-' . $array[0];
		return $datetime;
		}

	// Change date from YYYY-MM-DD to DD/MM/YYYY
	function datetime_to_date($datetime) {
		$array = explode("-", $datetime);
		$date = $array[2] . '/' . $array[1] . '/' . $array[0];
		return $date;
		}
		
	// Extract field id from its showed string
	function extract_relation_id($complete_name) {
		preg_match("#\[(.+?)\]#m",$complete_name,$group_match);
		if (!$group_match) {
			return null;
			}
		return $group_match[1];
		}
	
	// invert the order of the actual filter of a page if the fiel is the some
	function invert_order($order_type, $field) {
		if ( !$order_type ) return $field . ' ASC';
		$order_array = explode(' ', $order_type);
		if ( $order_array[0] != $field ) return $field . ' ASC';
		if ( $order_array[1] == 'ASC' )
			return $field . ' DESC';
		else
			return $field . ' ASC';
		}
		
	function get_order_icon($order_type, $field) {
		if ( !$order_type ) return 'up';
		$order_array = explode(' ', $order_type);
		if ( $order_array[0] != $field ) return 'up';
		if ( $order_array[1] == 'ASC' )
			return 'up';
		else
			return 'down';
		}

?>
