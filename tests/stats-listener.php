<?php

namespace Wporg\Tests;

use GP_Translation;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;
use Wporg\TranslationEvents\Tests\Translation_Factory;

class Stats_Listener_Test extends GP_UnitTestCase {
	private Translation_Factory $translation_factory;
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;

	public function setUp(): void {
		parent::setUp();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
	}

	public function test_does_not_store_action_for_draft_events() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$this->event_factory->create_draft();
		$this->event_factory->create_draft();

		$this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertEmpty( $stats );
	}

	public function test_does_not_store_action_for_inactive_events() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$this->event_factory->create_inactive_past( array( $user_id ) );
		$this->event_factory->create_inactive_future( array( $user_id ) );

		$this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertEmpty( $stats );
	}

	public function test_does_not_store_action_if_user_not_attending() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$this->event_factory->create_active();
		$this->event_factory->create_active();

		$this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertEmpty( $stats );
	}

	public function test_stores_action_create() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event1_id = $this->event_factory->create_active( array( $user_id ) );
		$event2_id = $this->event_factory->create_active( array( $user_id ) );

		$translation = $this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertCount( 2, $stats );

		$event1_stats = $this->stats_factory->get_by_event_id( $event1_id );
		$this->assertEquals( $event1_id, $event1_stats['event_id'] );
		$this->assertEquals( $user_id, $event1_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event1_stats['original_id'] );
		$this->assertEquals( 'create', $event1_stats['action'] );
		$this->assertEquals( 'aa', $event1_stats['locale'] );

		$event2_stats = $this->stats_factory->get_by_event_id( $event2_id );
		$this->assertEquals( $event2_id, $event2_stats['event_id'] );
		$this->assertEquals( $user_id, $event2_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event2_stats['original_id'] );
		$this->assertEquals( 'create', $event2_stats['action'] );
		$this->assertEquals( 'aa', $event2_stats['locale'] );
	}

	public function test_stores_action_approve() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event1_id = $this->event_factory->create_active( array( $user_id ) );
		$event2_id = $this->event_factory->create_active( array( $user_id ) );

		/** @var GP_Translation $translation */
		$translation = $this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.
		// Clean up stats because we won't care about the "created" action.
		$this->stats_factory->clean();

		$translation->set_as_current();
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertCount( 2, $stats );

		$event1_stats = $this->stats_factory->get_by_event_id( $event1_id );
		$this->assertEquals( $event1_id, $event1_stats['event_id'] );
		$this->assertEquals( $user_id, $event1_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event1_stats['original_id'] );
		$this->assertEquals( 'approve', $event1_stats['action'] );
		$this->assertEquals( 'aa', $event1_stats['locale'] );

		$event2_stats = $this->stats_factory->get_by_event_id( $event2_id );
		$this->assertEquals( $event2_id, $event2_stats['event_id'] );
		$this->assertEquals( $user_id, $event2_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event2_stats['original_id'] );
		$this->assertEquals( 'approve', $event2_stats['action'] );
		$this->assertEquals( 'aa', $event2_stats['locale'] );
	}

	public function test_stores_action_reject() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event1_id = $this->event_factory->create_active( array( $user_id ) );
		$event2_id = $this->event_factory->create_active( array( $user_id ) );

		/** @var GP_Translation $translation */
		$translation = $this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.
		// Clean up stats because we won't care about the "created" action.
		$this->stats_factory->clean();

		$translation->reject();
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertCount( 2, $stats );

		$event1_stats = $this->stats_factory->get_by_event_id( $event1_id );
		$this->assertEquals( $event1_id, $event1_stats['event_id'] );
		$this->assertEquals( $user_id, $event1_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event1_stats['original_id'] );
		$this->assertEquals( 'reject', $event1_stats['action'] );
		$this->assertEquals( 'aa', $event1_stats['locale'] );

		$event2_stats = $this->stats_factory->get_by_event_id( $event2_id );
		$this->assertEquals( $event2_id, $event2_stats['event_id'] );
		$this->assertEquals( $user_id, $event2_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event2_stats['original_id'] );
		$this->assertEquals( 'reject', $event2_stats['action'] );
		$this->assertEquals( 'aa', $event2_stats['locale'] );
	}

	public function test_stores_action_request_changes() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event1_id = $this->event_factory->create_active( array( $user_id ) );
		$event2_id = $this->event_factory->create_active( array( $user_id ) );

		/** @var GP_Translation $translation */
		$translation = $this->translation_factory->create( $user_id );
		// Stats_Listener will have been called.
		// Clean up stats because we won't care about the "created" action.
		$this->stats_factory->clean();

		$translation->set_as_changesrequested();
		// Stats_Listener will have been called.

		$stats = $this->stats_factory->get_all();
		$this->assertCount( 2, $stats );

		$event1_stats = $this->stats_factory->get_by_event_id( $event1_id );
		$this->assertEquals( $event1_id, $event1_stats['event_id'] );
		$this->assertEquals( $user_id, $event1_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event1_stats['original_id'] );
		$this->assertEquals( 'request_changes', $event1_stats['action'] );
		$this->assertEquals( 'aa', $event1_stats['locale'] );

		$event2_stats = $this->stats_factory->get_by_event_id( $event2_id );
		$this->assertEquals( $event2_id, $event2_stats['event_id'] );
		$this->assertEquals( $user_id, $event2_stats['user_id'] );
		$this->assertEquals( $translation->original_id, $event2_stats['original_id'] );
		$this->assertEquals( 'request_changes', $event2_stats['action'] );
		$this->assertEquals( 'aa', $event2_stats['locale'] );
	}
}
