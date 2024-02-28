<?php

namespace Wporg\TranslationEvents\Tests;

use wpdb;

class Stats_Factory {
	private wpdb $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function clean() {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$this->wpdb->query( 'delete from translate_event_actions' );
		// phpcs:enable
	}

	public function create( int $event_id, $user_id, $original_id, $action, $locale = 'aa' ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$this->wpdb->insert(
			'translate_event_actions',
			array(
				'event_id'    => $event_id,
				'user_id'     => $user_id,
				'original_id' => $original_id,
				'action'      => $action,
				'locale'      => $locale,
			)
		);
	}

	public function get_all(): array {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		return $this->wpdb->get_results( 'select * from translate_event_actions', ARRAY_A );
		// phpcs:enable
	}
}
