<?php

namespace Wporg\TranslationEvents;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

abstract class Event_Date extends DateTimeImmutable {
	abstract public function is_end_date(): bool;
	public $timezone;
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
		$this->timezone = $timezone;
	}

	/**
	 * Get the standard formatted text for the date in UTC.
	 *
	 * @return string The date text.
	 */
	public function __toString(): string {
		return $this->utc( 'Y-m-d H:i:s' );
	}
	/**
	 * Get the local formatted text for the date in UTC.
	 *
	 * @return string The date text.
	 */
	public function utc( $format ): string {
		return $this->setTimeZone( new DateTimeZone( 'UTC' ) )->format( $format );
	}

	public function is_in_the_past() {
		$current_date_time = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		return $this < $current_date_time;
	}

	/**
	 * Generate variable text depending on when the event starts or ends.
	 *
	 * @return string The end date text.
	 */
	public function get_variable_text(): string {
		$current_date_time = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		if ( ! $this->is_end_date() ) {
			if ( $this < $current_date_time ) {
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
		if ( $this < $current_date_time ) {
			return sprintf( 'ended %s', $this->format( 'l, F j, Y' ) );
		}

		return sprintf( 'until %s', $this->format( 'l, F j, Y' ) );
	}
}

class Event_Start_Date extends Event_Date {
	public function is_end_date(): bool {
		return false;
	}
}

class Event_End_Date extends Event_Date {
	public function is_end_date(): bool {
		return true;
	}
}
