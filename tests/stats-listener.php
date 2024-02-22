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

	private function get_stats(): array {
		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( 'select * from wp_wporg_gp_translation_events_actions', ARRAY_A );
		// phpcs:enable
	}

	public function test_does_not_store_action_for_inactive_events() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$this->event_factory->create_inactive_past( array( $user_id ) );
		$this->event_factory->create_inactive_future( array( $user_id ) );

		$this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->get_stats();
		$this->assertEmpty( $stats );
	}

	public function test_does_not_store_action_if_user_not_attending() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$this->event_factory->create_active();
		$this->event_factory->create_active();

		$this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->get_stats();
		$this->assertEmpty( $stats );
	}

	public function test_stores_action_create() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event1_id = $this->event_factory->create_active( array( $user_id ) );
		$event2_id = $this->event_factory->create_active( array( $user_id ) );

		$translation = $this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->get_stats();
		$this->assertCount( 2, $stats );

		$event1_stats = $stats[0];
		$this->assertEquals( $event1_id, $event1_stats['event_id'] );
		$this->assertEquals( $user_id, $event1_stats['user_id'] );
		$this->assertEquals( $translation->id, $event1_stats['translation_id'] );
		$this->assertEquals( 'create', $event1_stats['action'] );
		$this->assertEquals( 'aa', $event1_stats['locale'] );

		$event2_stats = $stats[1];
		$this->assertEquals( $event2_id, $event2_stats['event_id'] );
		$this->assertEquals( $user_id, $event2_stats['user_id'] );
		$this->assertEquals( $translation->id, $event2_stats['translation_id'] );
		$this->assertEquals( 'create', $event2_stats['action'] );
		$this->assertEquals( 'aa', $event2_stats['locale'] );
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
