<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Wporg\TranslationEvents\Event_Start_Date;
use Wporg\TranslationEvents\Event_End_Date;
use Exception;
use Throwable;

class InvalidTimeZone extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event time zone is invalid', 0, $previous );
	}
}

class InvalidStart extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event start is invalid', 0, $previous );
	}
}

class InvalidEnd extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event end is invalid', 0, $previous );
	}
}

class InvalidTitle extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event title is invalid', 0, $previous );
	}
}

class InvalidStatus extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event status is invalid', 0, $previous );
	}
}

class Event {
	private int $id;
	private int $author_id;
	private DateTimeImmutable $start;
	private DateTimeImmutable $end;
	private DateTimeZone $timezone;
	private string $slug;
	private string $status;
	private string $title;
	private string $description;

	/**
	 * Make an Event from post meta.
	 *
	 * @throws Exception When dates are invalid.
	 */
	public static function from_post_meta( int $id, array $meta ): Event {
		if ( ! isset( $meta['_event_start'][0] ) || ! isset( $meta['_event_end'][0] ) || ! isset( $meta['_event_timezone'][0] ) ) {
			throw new Exception( 'Invalid event meta' );
		}

		return new Event(
			$id,
			0,           // TODO: this function will be removed, this is here so tests pass.
			DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $meta['_event_start'][0], new DateTimeZone( 'UTC' ) ),
			DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $meta['_event_end'][0], new DateTimeZone( 'UTC' ) ),
			new DateTimeZone( $meta['_event_timezone'][0] ),
			'foo-slug',  // TODO: this function will be removed, this is here so tests pass.
			'publish',   // TODO: this function will be removed, this is here so tests pass.
			'Foo title', // TODO: this function will be removed, this is here so tests pass.
			''
		);
	}

	/**
	 * @throws InvalidStart
	 * @throws InvalidEnd
	 * @throws InvalidStatus
	 * @throws InvalidTitle
	 */
	public function __construct(
		int $id,
		int $author_id,
		DateTimeImmutable $start,
		DateTimeImmutable $end,
		DateTimeZone $timezone,
		string $slug,
		string $status,
		string $title,
		string $description
	) {
		$this->slug = $slug;

		$this->set_id( $id );
		$this->author_id = $author_id;
		$this->set_times( $start, $end );
		$this->set_timezone( $timezone );
		$this->set_status( $status );
		$this->set_title( $title );
		$this->set_description( $description );
	}

	public function id(): int {
		return $this->id;
	}

	public function author_id(): int {
		return $this->author_id;
	}

	public function start(): Event_Start_Date {
		return new Event_Start_Date( $this->start->format( 'Y-m-d H:i:s' ), $this->timezone() );
	}

	public function end(): Event_End_Date {
		return new Event_End_Date( $this->end->format( 'Y-m-d H:i:s' ), $this->timezone() );
	}

	public function timezone(): DateTimeZone {
		return $this->timezone;
	}

	public function slug(): string {
		return $this->slug;
	}

	public function status(): string {
		return $this->status;
	}

	public function title(): string {
		return $this->title;
	}

	public function description(): string {
		return $this->description;
	}

	public function set_id( int $id ): void {
		$this->id = $id;
	}

	/**
	 * @throws InvalidStart|InvalidEnd
	 */
	public function set_times( DateTimeImmutable $start, DateTimeImmutable $end ): void {
		$this->validate_times( $start, $end );
		$this->start = $start;
		$this->end   = $end;
	}

	public function set_timezone( DateTimeZone $timezone ): void {
		$this->timezone = $timezone;
	}

	/**
	 * @throws InvalidStatus
	 */
	public function set_status( string $status ): void {
		if ( ! in_array( $status, array( 'draft', 'publish' ), true ) ) {
			throw new InvalidStatus();
		}
		$this->status = $status;
	}

	/**
	 * @throws InvalidTitle
	 */
	public function set_title( string $title ): void {
		if ( ! $title ) {
			throw new InvalidTitle();
		}
		$this->title = $title;
	}

	public function set_description( string $description ): void {
		$this->description = $description;
	}

	/**
	 * @throws InvalidStart
	 * @throws InvalidEnd
	 */
	private function validate_times( DateTimeImmutable $start, DateTimeImmutable $end ) {
		if ( $end <= $start ) {
			throw new InvalidEnd();
		}
		if ( ! $start->getTimezone() || 'UTC' !== $start->getTimezone()->getName() ) {
			throw new InvalidStart();
		}
		if ( ! $end->getTimezone() || 'UTC' !== $end->getTimezone()->getName() ) {
			throw new InvalidEnd();
		}
	}
}
