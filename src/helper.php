<?php

final class Timer_Support_Candy_Helper {

	public static function get_days( $startDate, $endDate ) {
		return (int) $startDate->diff( $endDate )->format( '%r%a' );
	}

	public static function get_working_days( $startDate, $endDate ) {
		$sign = 1;
		if ( $startDate > $endDate ) {
			$sign      = - 1;
			$tmpDate   = $startDate;
			$startDate = $endDate;
			$endDate   = $tmpDate;
			unset( $tmpDate );
		}
		$holidays          = self::get_holidays( (int) $startDate->format( 'Y' ) );
		$endDate           = $endDate->getTimestamp();
		$startDate         = $startDate->getTimestamp();
		$days              = ( $endDate - $startDate ) / 86400; // + 1;
		$no_full_weeks     = floor( $days / 7 );
		$no_remaining_days = fmod( $days, 7 );

		$the_first_day_of_week = date( "N", $startDate );
		$the_last_day_of_week  = date( "N", $endDate );
		if ( $the_first_day_of_week <= $the_last_day_of_week ) {
			if ( $the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week ) {
				$no_remaining_days --;
			}
			if ( $the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week ) {
				$no_remaining_days --;
			}
		} else {
			if ( $the_first_day_of_week == 7 ) {
				$no_remaining_days --;
				if ( $the_last_day_of_week == 6 ) {
					$no_remaining_days --;
				}
			} else {
				$no_remaining_days -= 2;
			}
		}
		$workingDays = $no_full_weeks * 5;
		if ( $no_remaining_days > 0 ) {
			$workingDays += $no_remaining_days;
		}

		foreach ( $holidays as $holiday ) {
			$time_stamp = strtotime( $holiday );
			if ( $startDate <= $time_stamp && $time_stamp <= $endDate && date( "N", $time_stamp ) != 6 && date( "N", $time_stamp ) != 7 ) {
				$workingDays --;
			}
		}

		return $sign * $workingDays;
	}

	public static function get_holidays( $year ) {
		$days     = array(
			'01-01-',
			'06-01-',
			'01-05-',
			'03-05-',
			'05-06-',
			'16-06-',
			'15-08-',
			'01-11-',
			'11-11-',
			'25-12-',
			'26-12-'
		);
		$holidays = array();
		for ( $y = $year; $y <= $year + 1; $y ++ ) {
			foreach ( $days as $day ) {
				$holidays[] = $day . $y;
			}
			$holidays[] = date( 'd-m-Y', easter_date( $y ) + 24 * 60 * 60 );
		}

		return $holidays;
	}

	public static function add_days( $startDate, $days ) {
		return ( clone $startDate )->modify( '+' . intval( $days ) . ' day' );
	}

	public static function sub_days( $startDate, $days ) {
		return ( clone $startDate )->modify( '-' . intval( $days ) . ' day' );
	}

	public static function add_working_days( $startDate, $days ) {
		for ( $d = $days; $d <= $days * 2; $d ++ ) {
			$endDate = self::add_days( $startDate, $d );
			$diff    = (int) self::get_working_days( $startDate, $endDate );

			if ( $diff === $days ) {
				return $endDate;//->modify( '-1 day' );
			}
		}

		return self::add_days( $startDate, $days );
	}
}