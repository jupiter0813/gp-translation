<?php

namespace Wporg\Tests\Event;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;

class Event_Capabilities_Test extends GP_UnitTestCase {
	public function setUp(): void {
		parent::setUp();
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
		$this->attendee_repository = new Attendee_Repository();
		$this->event_repository    = new Event_Repository( $this->attendee_repository );
	}

	public function test_cannot_create_if_no_crud_permission() {
		$this->set_normal_user_as_current();

		add_filter( 'gp_translation_events_can_crud_event', '__return_false' );

		$this->assertFalse( current_user_can( 'create_translation_event' ) );
	}

	public function test_can_create_if_crud_permission() {
		$this->set_normal_user_as_current();
		get_current_user_id();

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );

		$this->assertTrue( current_user_can( 'create_translation_event' ) );
	}
}
