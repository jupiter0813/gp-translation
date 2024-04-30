<?php

namespace Wporg\TranslationEvents\Attendee;

use Exception;
use WP_User;

class Attendee_Repository {
	/**
	 * @throws Exception
	 */
	public function insert_attendee( Attendee $attendee ): void {
		global $wpdb, $gp_table_prefix;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"insert ignore into {$gp_table_prefix}event_attendees (event_id, user_id, is_host) values (%d, %d, %d)",
				array(
					'event_id' => $attendee->event_id(),
					'user_id'  => $attendee->user_id(),
					'is_host'  => $attendee->is_host() ? 1 : 0,
				),
			),
		);
		// phpcs:enable
	}

	/**
	 * Update an attendee.
	 *
	 * @param Attendee $attendee The attendee to update.
	 * @return void
	 */
	public function update_attendee( Attendee $attendee ): void {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			"{$gp_table_prefix}event_attendees",
			array( 'is_host' => $attendee->is_host() ? 1 : 0 ),
			array(
				'event_id' => $attendee->event_id(),
				'user_id'  => $attendee->user_id(),
			)
		);
		// phpcs:enable
	}

	/**
	 * @throws Exception
	 */
	public function remove_attendee( int $event_id, int $user_id ): void {
		global $wpdb, $gp_table_prefix;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete(
			"{$gp_table_prefix}event_attendees",
			array(
				'event_id' => $event_id,
				'user_id'  => $user_id,
			),
			array(
				'%d',
				'%d',
			),
		);
		// phpcs:enable
	}

	/**
	 * @throws Exception
	 */
	public function get_attendee( int $event_id, int $user_id ): ?Attendee {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"
				select
					user_id,
					is_host,
					(
						select group_concat( distinct locale )
						from {$gp_table_prefix}event_actions
						where event_id = attendees.event_id
						  and user_id = attendees.user_id
					) as locales
				from {$gp_table_prefix}event_attendees attendees
				where event_id = %d
				  and user_id = %d
			",
				array(
					$event_id,
					$user_id,
				),
			)
		);
		// phpcs:enable

		if ( ! $row ) {
			return null;
		}

		return new Attendee(
			$event_id,
			$row->user_id,
			'1' === $row->is_host,
			null === $row->locales ? array() : explode( ',', $row->locales ),
		);
	}

	/**
	 * Retrieve all the attendees of an event.
	 *
	 * @return Attendee[] Attendees of the event.
	 * @throws Exception
	 */
	public function get_attendees( int $event_id ): array {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"
				select
					user_id,
					is_host,
					(
						select group_concat( distinct locale )
						from {$gp_table_prefix}event_actions
						where event_id = attendees.event_id
						  and user_id = attendees.user_id
					) as locales
				from {$gp_table_prefix}event_attendees attendees
				where event_id = %d
			",
				array(
					$event_id,
				)
			)
		);
		// phpcs:enable

		return array_map(
			function ( $row ) use ( $event_id ) {
				return new Attendee(
					$event_id,
					$row->user_id,
					'1' === $row->is_host,
					null === $row->locales ? array() : explode( ',', $row->locales ),
				);
			},
			$rows,
		);
	}

	/**
	 * Get attendees without contributions for an event.
	 *
	 * @return Attendee[]
	 * @throws Exception
	 */
	public function get_attendees_not_contributing( int $event_id ): array {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$all_attendees = $wpdb->get_results(
			$wpdb->prepare(
				"
				select user_id, is_host
				from {$gp_table_prefix}event_attendees
				where event_id = %d
			",
				array(
					$event_id,
				)
			),
		);

		$contributing_ids = $wpdb->get_col(
			$wpdb->prepare(
				"
				select distinct user_id
				from {$gp_table_prefix}event_actions
				where event_id = %d
			",
				array(
					$event_id,
				)
			)
		);
		// phpcs:enable

		$attendees_not_contributing = array();
		foreach ( $all_attendees as $attendee_row ) {
			if ( ! in_array( $attendee_row->user_id, $contributing_ids, true ) ) {
				$attendees_not_contributing[] = new Attendee( $event_id, $attendee_row->user_id, '1' === $attendee_row->is_host );
			}
		}

		return $attendees_not_contributing;
	}

	/**
	 * Get contributors for an event.
	 */
	public function get_contributors( int $event_id ): array {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		// phpcs thinks we're doing a schema change but we aren't.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.SchemaChange
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"
				select user_id, group_concat( distinct locale ) as locales
				from {$gp_table_prefix}event_actions
				where event_id = %d
				group by user_id
			",
				array(
					$event_id,
				)
			)
		);
		// phpcs:enable

		$users = array();
		foreach ( $rows as $row ) {
			$user          = new WP_User( $row->user_id );
			$user->locales = explode( ',', $row->locales );
			$users[]       = $user;
		}

		uasort(
			$users,
			function ( $a, $b ) {
				return strcasecmp( $a->display_name, $b->display_name );
			}
		);

		return $users;
	}

	/**
	 * Get the hosts' users for an event.
	 *
	 * @param int $event_id The id of the event.
	 *
	 * @return Attendee[] The hosts of the event.
	 * @throws Exception
	 */
	public function get_hosts( int $event_id ): array {
		global $wpdb, $gp_table_prefix;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"
				select event_id, user_id
				from {$gp_table_prefix}event_attendees
				where event_id = %d and is_host = 1
			",
				array(
					$event_id,
				)
			)
		);
		// phpcs:enable

		$hosts = array();
		foreach ( $rows as $row ) {
			// TODO.
			$locales = array();
			$hosts[] = new Attendee( $row->event_id, $row->user_id, true, $locales );
		}
		return $hosts;
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
