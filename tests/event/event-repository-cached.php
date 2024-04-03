<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository_Cached;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event_End_Date;
use Wporg\TranslationEvents\Event_Start_Date;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Event_Repository_Cached_Test extends GP_UnitTestCase {
	private Event_Repository_Cached $repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->repository    = new Event_Repository_Cached( new Attendee_Repository() );

		wp_cache_delete( 'translation-events-active-events' );
		$this->set_normal_user_as_current();
	}

	public function test_get_active_events() {
		$now       = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$event1_id = $this->event_factory->create_active( array(), $now );
		$event2_id = $this->event_factory->create_active( array(), $now );
		$this->event_factory->create_active( array(), $now->modify( '+2 hours' ) );
		$this->event_factory->create_inactive_future();
		$this->event_factory->create_inactive_past();

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
		$event_id = $this->event_factory->create_active();
		$event    = $this->repository->get_event( $event_id );

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->update_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}

	public function test_invalidates_cache_when_events_are_deleted() {
		$event_id = $this->event_factory->create_active();
		$event    = $this->repository->get_event( $event_id );

		wp_cache_set( 'translation-events-active-events', 'foo' );
		$this->repository->delete_event( $event );
		$this->assertFalse( wp_cache_get( 'translation-events-active-events' ) );
	}
}
