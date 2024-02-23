<?php

namespace Wporg\Tests;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Stats_Calculator;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;

class Stats_Calculator_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Stats_Calculator $calculator;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->stats_factory = new Stats_Factory();
		$this->calculator    = new Stats_Calculator();
	}

	public function test_tells_that_event_has_no_stats() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event_id = $this->event_factory->create_active( array( $user_id ) );
		$event    = get_post( $event_id );

		$this->assertFalse( $this->calculator->event_has_stats( $event ) );
	}

	public function test_tells_that_event_has_stats() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event_id = $this->event_factory->create_active( array( $user_id ) );
		$event    = get_post( $event_id );

		$this->stats_factory->create( $event_id, $user_id, 1, 'create' );

		$this->assertTrue( $this->calculator->event_has_stats( $event ) );
	}
}
