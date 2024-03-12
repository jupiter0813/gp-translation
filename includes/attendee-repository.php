<?php

namespace Wporg\TranslationEvents;

use Exception;

class Attendee_Repository {
	private const USER_META_KEY = 'translation-events-attending';

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

		global $wpdb, $gp_table_prefix;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"insert ignore into {$gp_table_prefix}event_attendees (event_id, user_id) values (%d, %d)",
				array(
					'event_id' => $event_id,
					'user_id'  => $user_id,
				),
			),
		);
		// phpcs:enable
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

		global $wpdb, $gp_table_prefix;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"delete from {$gp_table_prefix}event_attendees where event_id = %d and user_id = %d",
				array(
					'event_id' => $event_id,
					'user_id'  => $user_id,
				),
			),
		);
		// phpcs:enable
	}

	public function is_attending( int $event_id, int $user_id ): bool {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"
				select count(*) as cnt
				from {$gp_table_prefix}event_attendees
				where event_id = %d
				  and user_id = %d
			",
				array(
					$event_id,
					$user_id,
				)
			)
		);
		// phpcs:enable

		return 1 === intval( $row->cnt );
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
	 *       it returns events that match a condition (have a user as attendee), so it belongs in an event repository.
	 *       However, since we don't have an event repository yet, the method is placed here for now.
	 *       When the method is moved to an event repository, it should return Event instances instead of event ids.
	 *
	 * @return int[] Event ids.
	 */
	public function get_events_for_user( int $user_id ): array {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"
				select event_id
				from {$gp_table_prefix}event_attendees
				where user_id = %d
			",
				array(
					$user_id,
				)
			)
		);
		// phpcs:enable

		return array_map(
			function ( object $row ) {
				return intval( $row->event_id );
			},
			$rows
		);
	}
}
