<?php

namespace Wporg\Tests\Stats;

use DateTimeImmutable;
use DateTimeZone;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Stats\Stats_Importer;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;
use Wporg\TranslationEvents\Tests\Translation_Factory;

class Stats_Importer_Test extends GP_UnitTestCase {
	private Translation_Factory $translation_factory;
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Stats_Importer $importer;
	private Event_Repository $event_repository;

	public function setUp(): void {
		parent::setUp();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
		$this->event_repository    = new Event_Repository( new Attendee_Repository() );
		$this->importer            = new Stats_Importer();
	}

	public function test_import_for_user_and_event() {
		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		// Create a translation before the event starts, which should not be imported.
		$this->translation_factory->create( $user_id );

		$event_id = $this->event_factory->create_active( array(), new DateTimeImmutable( '5 minutes ago', new DateTimeZone( 'UTC' ) ) );
		$event    = $this->event_repository->get_event( $event_id );

		// Create translations while the event is active.
		$translation1 = $this->translation_factory->create( $user_id );
		$translation2 = $this->translation_factory->create( $user_id );

		// Make sure no stats were created yet.
		$this->assertEquals( 0, $this->stats_factory->get_count() );

		$this->importer->import_for_user_and_event( $user_id, $event );

		$stats = $this->stats_factory->get_by_event_id( $event_id );
		$this->assertCount( 2, $stats );

		$stats1 = $stats[0];
		$this->assertEquals( $event_id, $stats1['event_id'] );
		$this->assertEquals( $user_id, $stats1['user_id'] );
		$this->assertEquals( $translation1->original_id, $stats1['original_id'] );
		$this->assertEquals( 'create', $stats1['action'] );
		$this->assertEquals( 'default', $stats1['locale'] );
		$this->assertEquals( $translation1->date_added, $stats1['happened_at'] );

		$stats2 = $stats[1];
		$this->assertEquals( $event_id, $stats2['event_id'] );
		$this->assertEquals( $user_id, $stats2['user_id'] );
		$this->assertEquals( $translation2->original_id, $stats2['original_id'] );
		$this->assertEquals( 'create', $stats2['action'] );
		$this->assertEquals( 'default', $stats2['locale'] );
		$this->assertEquals( $translation2->date_added, $stats1['happened_at'] );
	}
}
