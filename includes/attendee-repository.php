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

	/**
	 * @throws Exception
	 */
	public function remove_attendee( int $event_id, int $user_id ): void {
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

		if ( isset( $event_ids[ $event_id ] ) ) {
			unset( $event_ids[ $event_id ] );
		}

		update_user_meta( $user_id, self::USER_META_KEY, $event_ids );
	}

	public function is_attending( int $event_id, int $user_id ): bool {
		$event_ids = get_user_meta( $user_id, self::USER_META_KEY, true );
		if ( ! $event_ids ) {
			$event_ids = array();
		}

		return isset( $event_ids[ $event_id ] );
	}

	/**
	 * @return int[] User ids.
	 */
	public function get_attendees( int $event_id ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// TODO.
		return array();
	}

	/**
	 * @deprecated
	 * TODO: This method should be moved out of this class because it's not about attendance,
	 *       it returns events that match a condition (belong to a user), so it belongs in an event repository.
	 *       However, since we don't have an event repository yet, the method is placed here for now.
	 *       When the method is moved to an event repository, it should return Event instances instead of event ids.
	 *
	 * @return int[] Event ids.
	 */
	public function get_events_for_user( int $user_id ): array {
		$event_ids = get_user_meta( $user_id, self::USER_META_KEY, true );
		if ( ! $event_ids ) {
			$event_ids = array();
		}

		return array_keys( $event_ids );
	}
}
