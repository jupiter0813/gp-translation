<?php

namespace Wporg\TranslationEvents\Tests;

use wpdb;

class Stats_Factory {
	public function clean() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( "delete from {$wpdb->base_prefix}event_actions" );
		// phpcs:enable
	}

	public function create( int $event_id, $user_id, $original_id, $action, $locale = 'aa' ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$wpdb->base_prefix . 'event_actions',
			array(
				'event_id'    => $event_id,
				'user_id'     => $user_id,
				'original_id' => $original_id,
				'action'      => $action,
				'locale'      => $locale,
			)
		);
	}

	public function get_count(): int {
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		return intval( $wpdb->get_var( "select count(*) from {$wpdb->base_prefix}event_actions" ) );
		// phpcs:enable
	}

	public function get_by_event_id( $event_id ): array {
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->base_prefix}event_actions where event_id = %s", $event_id ), ARRAY_A );
		// phpcs:enable
	}
}
