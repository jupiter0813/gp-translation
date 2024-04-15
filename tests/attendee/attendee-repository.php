<?php

namespace Wporg\Tests\Attendee;

use WP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;

class Attendee_Repository_Test extends WP_UnitTestCase {
	private Attendee_Repository $repository;

	protected function setUp(): void {
		parent::setUp();
		$this->repository = new Attendee_Repository();
	}

	public function test_add_attendee_invalid_event_id() {
		$this->expectExceptionMessage( 'invalid event id' );
		$this->repository->insert_attendee( new Attendee( 0, 1 ) );
	}

	public function test_add_attendee_invalid_user_id() {
		$this->expectExceptionMessage( 'invalid user id' );
		$this->repository->insert_attendee( new Attendee( 1, 0 ) );
	}

	public function test_insert_attendee() {
		$event1_id = 1;
		$event2_id = 2;
		$user_id   = 42;

		$this->repository->insert_attendee( new Attendee( $event1_id, $user_id ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, $user_id ) );

		$rows = $this->all_table_rows();
		$this->assertCount( 2, $rows );
		$this->assertEquals( $event1_id, $rows[0]->event_id );
		$this->assertEquals( $event2_id, $rows[1]->event_id );
	}

	public function test_remove_attendee() {
		$event1_id = 1;
		$event2_id = 2;
		$user_id   = 42;

		$this->repository->insert_attendee( new Attendee( $event1_id, $user_id ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, $user_id ) );

		$this->repository->remove_attendee( $event1_id, $user_id );

		$rows = $this->all_table_rows();
		$this->assertCount( 1, $rows );
		$this->assertEquals( $event2_id, $rows[0]->event_id );
	}

	public function test_get_attendee() {
		$event1_id = 1;
		$event2_id = 2;
		$user1_id  = 42;
		$user2_id  = 43;

		$attendee11 = new Attendee( $event1_id, $user1_id );
		$attendee11->mark_as_host();
		$this->repository->insert_attendee( $attendee11 );

		$attendee12 = new Attendee( $event1_id, $user2_id );
		$this->repository->insert_attendee( $attendee12 );

		// Add some more attendees to make sure we get the right ones.
		$this->repository->insert_attendee( new Attendee( $event1_id, 84 ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, 84 ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, $user1_id ) );

		$retrieved_attendee_11 = $this->repository->get_attendee( $event1_id, $user1_id );
		$this->assertEquals( $attendee11, $retrieved_attendee_11 );
		$this->assertTrue( $retrieved_attendee_11->is_host() );

		$retrieved_attendee_12 = $this->repository->get_attendee( $event1_id, $user2_id );
		$this->assertEquals( $attendee12, $retrieved_attendee_12 );
		$this->assertFalse( $retrieved_attendee_12->is_host() );

		$this->assertNull( $this->repository->get_attendee( $event2_id, $user2_id ) );
	}

	public function test_get_hosts() {
		$event1_id = 1;
		$event2_id = 2;
		$user1_id  = 42;
		$user2_id  = 43;

		$host11 = new Attendee( $event1_id, $user1_id );
		$host11->mark_as_host();
		$this->repository->insert_attendee( $host11 );

		$host12 = new Attendee( $event1_id, $user2_id );
		$host12->mark_as_host();
		$this->repository->insert_attendee( $host12 );

		$host21 = new Attendee( $event2_id, $user1_id );
		$host21->mark_as_host();
		$this->repository->insert_attendee( $host21 );

		// Add some more attendees to make sure we get the right ones.
		$this->repository->insert_attendee( new Attendee( $event1_id, 84 ) );
		$this->repository->insert_attendee( new Attendee( $event1_id, 85 ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, 84 ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, $user1_id ) );

		$hosts = $this->repository->get_hosts( $event1_id );
		$this->assertCount( 2, $hosts );
		$this->assertEquals( $host11, $hosts[0] );
		$this->assertEquals( $host12, $hosts[1] );

		$hosts = $this->repository->get_hosts( $event2_id );
		$this->assertCount( 1, $hosts );
		$this->assertEquals( $host21, $hosts[0] );
	}

	public function test_get_events_for_user() {
		$event1_id    = 1;
		$event2_id    = 2;
		$event3_id    = 3;
		$user_id      = 42;
		$another_user = $user_id + 1;

		$this->repository->insert_attendee( new Attendee( $event1_id, $user_id ) );
		$this->repository->insert_attendee( new Attendee( $event2_id, $user_id ) );
		$this->repository->insert_attendee( new Attendee( $event3_id, $another_user ) );

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
