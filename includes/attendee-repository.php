<?php

namespace Wporg\TranslationEvents;

use Exception;

class Attendee_Repository {
	public const USER_META_KEY = 'translation-events-attending';

	/**
	 * @throws Exception
	 */
	public function add_attendee( int $event_id, int $user_id ): void {
		if ( $event_id < 1 ) {
			throw new Exception( 'invalid event id' );
		}
		if ( $user_id < 1 ) {
			throw new Exception( 'invalid user id' );
		}

		$event_ids = get_user_meta( $user_id, self::USER_META_KEY, true );
		if ( ! $event_ids ) {
			$event_ids = array();
		}
		$event_ids[ $event_id ] = true;
		update_user_meta( $user_id, self::USER_META_KEY, $event_ids );
	}

	public function remove_attendee( int $event_id, int $user_id ): void {
		// TODO.
	}

	public function is_attending( int $event_id, int $user_id ): bool {
		// TODO.
		return false;
	}

	/**
	 * @return int[] User ids.
	 */
	public function get_attendees( int $event_id ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// TODO.
		return array();
	}
}
