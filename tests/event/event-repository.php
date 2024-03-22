<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Event_Repository_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;
	private Event_Repository $repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->repository    = new Event_Repository( new Attendee_Repository() );

		$this->set_normal_user_as_current();
	}

	public function test_get_event_returns_null_when_post_does_not_not_have_correct_type() {
		$id = wp_insert_post(
			array(
				'post_type' => 'foo',
			)
		);
		$this->assertNull( $this->repository->get_event( $id ) );
	}

	public function test_get_event_returns_null_when_event_does_not_exist() {
		$this->assertNull( $this->repository->get_event( 42 ) );
	}

	public function test_get_event_returns_null_when_post_is_not_event() {
		$post_id = $this->factory()->post->create();
		$this->assertNull( $this->repository->get_event( $post_id ) );
	}

	public function test_get_event() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$start    = $now->modify( '-1 hours' );
		$end      = $now->modify( '+1 hours' );
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$event_id = $this->event_factory->create_event( $start, $end, $timezone, array() );
		$event    = $this->repository->get_event( $event_id );

		$this->assertEquals( $start->getTimestamp(), $event->start()->getTimestamp() );
		$this->assertEquals( $end->getTimestamp(), $event->end()->getTimestamp() );
		$this->assertEquals( $timezone, $event->timezone() );
		$this->assertStringStartsWith( 'event-title-', $event->slug() );
		$this->assertEquals( 'publish', $event->status() );
		$this->assertStringStartsWith( 'Event title', $event->title() );
		$this->assertStringStartsWith( 'Event content', $event->description() );
	}

	public function test_create_event() {
		$now         = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$start       = $now->modify( '-1 hours' );
		$end         = $now->modify( '+1 hours' );
		$timezone    = new DateTimeZone( 'Europe/Lisbon' );
		$slug        = 'foo-slug';
		$status      = 'publish';
		$title       = 'Foo title';
		$description = 'Foo Description';

		$event = new Event(
			0,
			0,
			$start,
			$end,
			$timezone,
			$slug,
			$status,
			$title,
			$description,
		);

		$this->repository->insert_event( $event );
		$this->assertGreaterThan( 0, $event->id() );

		$created_event = $this->repository->get_event( $event->id() );

		$this->assertEquals( $start->getTimestamp(), $created_event->start()->getTimestamp() );
		$this->assertEquals( $end->getTimestamp(), $created_event->end()->getTimestamp() );
		$this->assertEquals( $timezone, $created_event->timezone() );
		$this->assertEquals( $slug, $created_event->slug() );
		$this->assertEquals( $status, $created_event->status() );
		$this->assertEquals( $title, $created_event->title() );
		$this->assertEquals( $description, $created_event->description() );
	}

	public function test_update_event() {
		$event_id = $this->event_factory->create_active();
		$event    = $this->repository->get_event( $event_id );

		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		// phpcs:disable Squiz.PHP.DisallowMultipleAssignments.Found
		$event->set_times( $updated_start = $now->modify( '+1 days' ), $updated_end = $now->modify( '+2 days' ) );
		$event->set_timezone( $updated_timezone = new DateTimeZone( 'Europe/Madrid' ) );
		$event->set_status( $updated_status = 'draft' );
		$event->set_title( $updated_title = 'Updated title' );
		$event->set_description( $updated_description = 'Updated description' );
		// phpcs:enable

		$this->repository->update_event( $event );
		$updated_event = $this->repository->get_event( $event_id );

		$this->assertEquals( $updated_start->getTimestamp(), $updated_event->start()->utc()->getTimestamp() );
		$this->assertEquals( $updated_end->getTimestamp(), $updated_event->end()->utc()->getTimestamp() );
		$this->assertEquals( $updated_timezone->getName(), $updated_event->timezone()->getName() );
		$this->assertEquals( $updated_status, $updated_event->status() );
		$this->assertEquals( $updated_title, $updated_event->title() );
		$this->assertEquals( $updated_description, $updated_event->description() );
	}

	public function test_delete_event() {
		$event_id = $this->event_factory->create_active();

		$event = $this->repository->get_event( $event_id );
		$this->repository->delete_event( $event );

		$this->assertNull( $this->repository->get_event( $event_id ) );
	}

	public function test_get_active_events() {
		$now       = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$event1_id = $this->event_factory->create_active( array(), $now );
		$event2_id = $this->event_factory->create_active( array(), $now );
		$this->event_factory->create_active( array(), $now->modify( '+2 hours' ) );
		$this->event_factory->create_inactive_future();
		$this->event_factory->create_inactive_past();

		$events = $this->repository->get_current_events()->events;
		$this->assertCount( 2, $events );
		$this->assertEquals( $event1_id, $events[0]->id() );
		$this->assertEquals( $event2_id, $events[1]->id() );

		$result = $this->repository->get_current_events( 1, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event1_id, $events[0]->id() );

		$result = $this->repository->get_current_events( 2, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event2_id, $events[0]->id() );
	}

	public function test_get_current_events_for_user() {
		$user_id   = $this->set_normal_user_as_current();
		$now       = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$event1_id = $this->event_factory->create_active( array( $user_id ), $now );
		$event2_id = $this->event_factory->create_active( array( $user_id ), $now );
		$this->event_factory->create_active( array(), $now );
		$this->event_factory->create_active( array(), $now->modify( '+2 hours' ) );
		$this->event_factory->create_inactive_future();
		$this->event_factory->create_inactive_past();

		$events = $this->repository->get_current_events_for_user( $user_id )->events;
		$this->assertCount( 2, $events );
		$this->assertEquals( $event1_id, $events[0]->id() );
		$this->assertEquals( $event2_id, $events[1]->id() );

		$result = $this->repository->get_current_events_for_user( $user_id, 1, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event1_id, $events[0]->id() );

		$result = $this->repository->get_current_events_for_user( $user_id, 2, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event2_id, $events[0]->id() );
	}

	public function test_get_past_events_for_user() {
		$user_id = $this->set_normal_user_as_current();

		$event1_id = $this->event_factory->create_inactive_past( array( $user_id ) );
		$event2_id = $this->event_factory->create_inactive_past( array( $user_id ) );

		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$this->event_factory->create_active( array( $user_id ), $now );
		$this->event_factory->create_active( array( $user_id ), $now );

		$events = $this->repository->get_past_events_for_user( $user_id )->events;
		$this->assertCount( 2, $events );
		$this->assertEquals( $event1_id, $events[0]->id() );
		$this->assertEquals( $event2_id, $events[1]->id() );

		$result = $this->repository->get_past_events_for_user( $user_id, 1, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event1_id, $events[0]->id() );

		$result = $this->repository->get_past_events_for_user( $user_id, 2, 1 );
		$events = $result->events;
		$this->assertCount( 1, $events );
		$this->assertEquals( 2, $result->page_count );
		$this->assertEquals( $event2_id, $events[0]->id() );
	}

	public function test_get_events_created_by_user() {
		$user_id   = $this->set_normal_user_as_current();
		$event1_id = $this->event_factory->create_inactive_past();
		$event2_id = $this->event_factory->create_active();
		$event3_id = $this->event_factory->create_active();
		$event4_id = $this->event_factory->create_inactive_future();

		$this->set_admin_user_as_current();
		$this->event_factory->create_inactive_past();
		$this->event_factory->create_active();
		$this->event_factory->create_inactive_future();

		$events = $this->repository->get_events_created_by_user( $user_id )->events;

		$this->assertCount( 4, $events );
		$this->assertEquals( $event1_id, $events[0]->id() );
		$this->assertEquals( $event2_id, $events[1]->id() );
		$this->assertEquals( $event3_id, $events[2]->id() );
		$this->assertEquals( $event4_id, $events[3]->id() );
	}
}
