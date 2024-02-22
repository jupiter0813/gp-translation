<?php

namespace Wporg\Tests;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Translation_Factory;

class Stats_Listener_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;

	public function setUp(): void {
		parent::setUp();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->event_factory       = new Event_Factory();
	}

	public function test_does_not_store_action_for_inactive_event() {
		$this->markTestSkipped( 'TODO' );
	}

	public function test_does_not_store_action_if_user_not_attending() {
		$this->markTestSkipped( 'TODO' );
	}

	public function test_stores_action_create() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event_id    = $this->event_factory->create_active( array( $user_id ) );
		$translation = $this->translation_factory->create( $user_id );

		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results( 'select * from wp_wporg_gp_translation_events_actions' );
		// phpcs:enable

		// TODO.
		$this->assertNotEmpty( $rows );
	}

	public function test_stores_action_approve() {
		$this->markTestSkipped( 'TODO' );
	}

	public function test_stores_action_reject() {
		$this->markTestSkipped( 'TODO' );
	}

	public function test_stores_action_request_changes() {
		$this->markTestSkipped( 'TODO' );
	}
}
