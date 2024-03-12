<?php

namespace Wporg\Tests;

use WP_UnitTestCase;
use Wporg\TranslationEvents\Attendee_Repository;

class Attendee_Repository_Test extends WP_UnitTestCase {
	private Attendee_Repository $repository;

	protected function setUp(): void {
		parent::setUp();
		$this->repository = new Attendee_Repository();
	}

	public function test_add_attendee_invalid_event_id() {
		$this->expectExceptionMessage( 'invalid event id' );
		$this->repository->add_attendee( 0, 1 );
	}

	public function test_add_attendee_invalid_user_id() {
		$this->expectExceptionMessage( 'invalid user id' );
		$this->repository->add_attendee( 1, 0 );
	}

	public function test_add_attendee() {
		$event1_id = 1;
		$event2_id = 2;
		$user_id   = 42;

		$this->repository->add_attendee( $event1_id, $user_id );
		$this->repository->add_attendee( $event2_id, $user_id );

		$event_ids = get_user_meta( $user_id, 'translation-events-attending', true );
		$this->assertCount( 2, $event_ids );
		$this->assertTrue( $event_ids[ $event1_id ] );
		$this->assertTrue( $event_ids[ $event2_id ] );

		$event_ids_another_user = get_user_meta( $user_id + 1, 'translation-events-attending', true );
		$this->assertEmpty( $event_ids_another_user );
	}

	public function test_remove_attendee_invalid_event_id() {
		$this->expectExceptionMessage( 'invalid event id' );
		$this->repository->remove_attendee( 0, 1 );
	}

	public function test_remove_attendee_invalid_user_id() {
		$this->expectExceptionMessage( 'invalid user id' );
		$this->repository->remove_attendee( 1, 0 );
	}

	public function test_remove_attendee() {
		$event1_id = 1;
		$event2_id = 2;
		$user_id   = 42;

		$this->repository->add_attendee( $event1_id, $user_id );
		$this->repository->add_attendee( $event2_id, $user_id );

		$this->repository->remove_attendee( $event1_id, $user_id );

		$event_ids = get_user_meta( $user_id, 'translation-events-attending', true );
		$this->assertCount( 1, $event_ids );
		$this->assertTrue( $event_ids[ $event2_id ] );
	}

	public function test_is_attending() {
		$event1_id = 1;
		$event2_id = 2;
		$user_id   = 42;

		$this->repository->add_attendee( $event1_id, $user_id );

		$this->assertTrue( $this->repository->is_attending( $event1_id, $user_id ) );
		$this->assertFalse( $this->repository->is_attending( $event2_id, $user_id ) );
	}

	public function test_get_event_ids() {
		$event1_id    = 1;
		$event2_id    = 2;
		$event3_id    = 3;
		$user_id      = 42;
		$another_user = $user_id + 1;

		$this->repository->add_attendee( $event1_id, $user_id );
		$this->repository->add_attendee( $event2_id, $user_id );
		$this->repository->add_attendee( $event3_id, $another_user );

		$this->assertEquals( array( $event1_id, $event2_id ), $this->repository->get_events_for_user( $user_id ) );
		$this->assertEquals( array( $event3_id ), $this->repository->get_events_for_user( $another_user ) );
		$this->assertEmpty( $this->repository->get_events_for_user( $another_user + 1 ) );
	}

	private function all_table_rows(): array {
		global $wpdb, $gp_table_prefix;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( "select * from {$gp_table_prefix}event_attendees order by event_id, user_id" );
		// phpcs:enable
	}
}
