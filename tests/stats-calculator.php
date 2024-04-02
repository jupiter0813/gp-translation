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

		$this->assertFalse( $this->calculator->event_has_stats( $event_id ) );
	}

	public function test_tells_that_event_has_stats() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$event_id = $this->event_factory->create_active( array( $user_id ) );
		$event    = get_post( $event_id );

		$this->stats_factory->create( $event_id, $user_id, 1, 'create' );

		$this->assertTrue( $this->calculator->event_has_stats( $event_id ) );
	}

	public function test_calculates_stats_for_event() {
		$this->set_normal_user_as_current();
		$user1_id = 42;
		$user2_id = 43;
		$user3_id = 44;

		$event1_id = $this->event_factory->create_active( array( $user1_id ) );
		$event2_id = $this->event_factory->create_active( array( $user1_id ) );

		// For event1, aa locale, multiple users.
		$this->stats_factory->create( $event1_id, $user1_id, 11, 'create' );
		$this->stats_factory->create( $event1_id, $user1_id, 12, 'create' );
		$this->stats_factory->create( $event1_id, $user2_id, 13, 'create' );
		$this->stats_factory->create( $event1_id, $user2_id, 11, 'approve' );
		$this->stats_factory->create( $event1_id, $user2_id, 12, 'reject' );
		$this->stats_factory->create( $event1_id, $user3_id, 13, 'request_changes' );

		// For event1, bb locale, multiple users.
		$this->stats_factory->create( $event1_id, $user1_id, 21, 'create', 'bb' );
		$this->stats_factory->create( $event1_id, $user2_id, 22, 'create', 'bb' );
		$this->stats_factory->create( $event1_id, $user3_id, 21, 'approve', 'bb' );
		$this->stats_factory->create( $event1_id, $user3_id, 22, 'request_changes', 'bb' );

		// For event2, which should not be included in the stats.
		$this->stats_factory->create( $event2_id, $user1_id, 31, 'create' );
		$this->stats_factory->create( $event2_id, $user1_id, 32, 'create' );
		$this->stats_factory->create( $event2_id, $user2_id, 31, 'approve' );
		$this->stats_factory->create( $event2_id, $user2_id, 32, 'reject' );

		$event1 = get_post( $event1_id );
		$stats  = $this->calculator->for_event( $event1->ID );

		$this->assertCount( 2, $stats->rows() );

		// Locale aa.
		$this->assertEquals( 3, $stats->rows()['aa']->created );
		$this->assertEquals( 3, $stats->rows()['aa']->reviewed );
		$this->assertEquals( 3, $stats->rows()['aa']->users );

		// Locale bb.
		$this->assertEquals( 2, $stats->rows()['bb']->created );
		$this->assertEquals( 2, $stats->rows()['bb']->reviewed );
		$this->assertEquals( 3, $stats->rows()['bb']->users );

		// Totals.
		$this->assertEquals( 5, $stats->totals()->created );
		$this->assertEquals( 5, $stats->totals()->reviewed );
		$this->assertEquals( 3, $stats->totals()->users );
	}
}
