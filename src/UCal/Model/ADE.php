<?php
namespace UCal\Model;

class ADE
{
	public static function getCurrentWeek($offset = 0) {
		//TODO
		return date('W')+19+$offset;
	}

	public static function getWeeksRelative(array $weeksOffsets) {
		$weeks = array();

		foreach ($weeksOffsets as $weekOffset) {
			$weeks[] = self::getCurrentWeek($weekOffset);
		}
		
		return $weeks;
	}
}