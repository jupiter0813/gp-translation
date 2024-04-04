<?php

namespace Wporg\TranslationEvents\Attendee;

use Exception;

class Attendee {
	private int $event_id;
	private int $user_id;

	/**
	 * @throws Exception
	 */
	public function __construct( int $event_id, int $user_id ) {
		if ( $event_id < 1 ) {
			throw new Exception( 'invalid event id' );
		}
		if ( $user_id < 1 ) {
			throw new Exception( 'invalid user id' );
		}

		$this->event_id = $event_id;
		$this->user_id  = $user_id;
	}

	public function event_id(): int {
		return $this->event_id;
	}

	public function user_id(): int {
		return $this->user_id;
	}
}
