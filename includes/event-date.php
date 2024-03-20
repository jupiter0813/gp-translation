<?php

namespace Wporg\TranslationEvents;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Event_Date
 *
 * The event date is in local time, get the UTC time via the utc() method.
 *
 * @package Wporg\TranslationEvents
 */
abstract class Event_Date extends DateTimeImmutable {
	protected $event_timezone;
	public function __construct( string $date, DateTimeZone $timezone = null ) {
		if ( ! $timezone ) {
			$timezone = new DateTimeZone( 'UTC' );
		}

		try {
			$utc_date = new DateTime( $date, new DateTimeZone( 'UTC' ) );
			$utc_date->setTimezone( $timezone );
		} catch ( Exception $e ) {
			$utc_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		}

		parent::__construct( $utc_date->format( 'Y-m-d H:i:s' ), $timezone );
		$this->event_timezone = $timezone;
	}

	public function timezone() {
		return $this->event_timezone;
	}

	/**
	 * Get the standard formatted text for the date in UTC.
	 *
	 * @return string The date text.
	 */
	public function __toString(): string {
		return $this->utc()->format( 'Y-m-d H:i:s' );
	}
	/**
	 * Get the local formatted text for the date in UTC.
	 *
	 * @return DateTimeImmutable The date text.
	 */
	public function utc(): DateTimeImmutable {
		return $this->setTimeZone( new DateTimeZone( 'UTC' ) );
	}

	public function is_in_the_past() {
		$current_date_time = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		return $this->utc() < $current_date_time;
	}

	/**
	 * Generate variable text depending on when the event starts or ends.
	 *
	 * @return string The end date text.
	 */
	public function get_variable_text(): string {
		$current_date_time = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		if ( $this instanceof Event_Start_Date ) {
			if ( $this->is_in_the_past() ) {
				return sprintf( 'started %s', $this->format( 'l, F j, Y' ) );
			}
			return sprintf( 'starts %s', $this->format( 'l, F j, Y' ) );
		}

		$interval       = $this->diff( $current_date_time );
		$hours_left     = ( $interval->d * 24 ) + $interval->h;
		$hours_in_a_day = 24;

		if ( 0 === $hours_left ) {
			/* translators: %s: Number of minutes left. */
			return sprintf( _n( 'ends in %s minute', 'ends in %s minutes', $interval->i ), $interval->i );
		} elseif ( $hours_left <= $hours_in_a_day ) {
			/* translators: %s: Number of hours left. */
			return sprintf( _n( 'ends in %s hour', 'ends in %s hours', $hours_left ), $hours_left );
		}
		if ( $this->is_in_the_past() ) {
			return sprintf( 'ended %s', $this->format( 'l, F j, Y' ) );
		}

		return sprintf( 'until %s', $this->format( 'l, F j, Y' ) );
	}
}

class Event_Start_Date extends Event_Date {
}

class Event_End_Date extends Event_Date {
}
