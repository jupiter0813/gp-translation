<?php

namespace Wporg\Tests\Event;

use DateTimeZone;
use Wporg\Tests\Base_Test;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository_Cached;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\Event_End_Date;
use Wporg\TranslationEvents\Event\Event_Start_Date;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Event_Repository_Cached_Test extends Base_Test {
	private Event_Repository_Cached $repository;
	private Event_Factory $event_factory;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->repository    = new Event_Repository_Cached( $this->now, new Attendee_Repository() );

		wp_cache_delete( 'translation-events-active-events' );
		$this->set_normal_user_as_current();
	}

	public function test_get_current_events_when_no_current_events_exist() {
		$result = $this->repository->get_current_events();
		$this->assertIsArray( $result->events );
		$this->assertEmpty( $result->events );

		$result = $this->repository->get_current_events( 1, 1 );
		$this->assertIsArray( $result->events );
		$this->assertEmpty( $result->events );

		$result = $this->repository->get_current_events( 2, 1 );
		$this->assertIsArray( $result->events );
		$this->assertEmpty( $result->events );
	}

	public function test_get_current_events() {
		$event1_id = $this->event_factory->create_active( $this->now );
		$event2_id = $this->event_factory->create_active( $this->now );
		$this->event_factory->create_active( $this->now->modify( '+2 hours' ) );
		$this->event_factory->create_inactive_future( $this->now );
		$this->event_factory->create_inactive_past( $this->now );

		$result = $this->repository->get_current_events();
		$events = $result->events;
		$this->assertCount( 2, $events );
		$this->assertEquals( 1, $result->page_count );
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

	public function test_invalidates_cache_when_events_are_created() {
		$event = new Event(
			0,
			new Event_Start_Date( 'now' ),
			( new Event_End_Date( 'now' ) )->modify( '+1 hour' ),
			new DateTimeZone( 'Europe/Lisbon' ),
			'draft',
			'Foo',
			'Foo.'
		);

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->insert_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}

	public function test_invalidates_cache_when_events_are_updated() {
		$event_id = $this->event_factory->create_active( $this->now );
		$event    = $this->repository->get_event( $event_id );

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->update_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}

	public function test_invalidates_cache_when_events_are_trashed() {
		$event_id = $this->event_factory->create_active( $this->now );
		$event    = $this->repository->get_event( $event_id );

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->trash_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}

	public function test_invalidates_cache_when_events_are_deleted() {
		$event_id = $this->event_factory->create_active( $this->now );
		$event    = $this->repository->get_event( $event_id );

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->delete_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}
}
