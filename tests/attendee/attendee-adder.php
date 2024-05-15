<?php
namespace Wporg\Tests\Attendee;

use DateTimeImmutable;
use DateTimeZone;
use GP_Translation;
use GP_UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Adder;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;
use Wporg\TranslationEvents\Tests\Translation_Factory;
use Wporg\TranslationEvents\Translation\Translation_Repository;

class Attendee_Adder_Test extends GP_UnitTestCase {
	/**
	 * @var MockObject|Attendee_Repository
	 */
	private $attendee_repository;

	private Attendee_Adder $adder;
	private Event_Repository $event_repository;
	private Event_Factory $event_factory;
	private Translation_Factory $translation_factory;
	private Stats_Factory $stats_factory;

	public function setUp(): void {
		parent::setUp();
		$this->attendee_repository = $this->createMock( Attendee_Repository::class );
		$this->adder               = new Attendee_Adder( $this->attendee_repository, new Translation_Repository() );
		$this->event_repository    = new Event_Repository( new Attendee_Repository() );
		$this->event_factory       = new Event_Factory();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->stats_factory       = new Stats_Factory();

		$this->set_normal_user_as_current();
	}

	public function test_add() {
		$user_id  = get_current_user_id();
		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );
		$attendee = new Attendee( $event_id, $user_id );

		$this->attendee_repository
			->expects( $this->once() )
			->method( 'insert_attendee' )
			->with( $this->equalTo( $attendee ) );

		$this->adder->add_to_event( $event, $attendee );
	}

	public function test_sets_is_new_contributor() {
		$this->set_normal_user_as_current();
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$user1_id = 52;
		$user2_id = 53;
		$user3_id = 54;

		// Create 10 translations for $user2_id before event start.
		for ( $i = 0; $i < 10; $i++ ) {
			$this->translation_factory->create( $user2_id );

		}
		// Create 11 translations for $user3_id before event start.
		for ( $i = 0; $i < 11; $i++ ) {
			$this->translation_factory->create( $user3_id );
		}

		$event1_id  = $this->event_factory->create_active( array(), $now->modify( '+1 day' ) );
		$event1     = $this->event_repository->get_event( $event1_id );
		$attendee11 = new Attendee( $event1_id, $user1_id );
		$attendee12 = new Attendee( $event1_id, $user2_id );
		$attendee13 = new Attendee( $event1_id, $user3_id );
		$this->adder->add_to_event( $event1, $attendee11 );
		$this->adder->add_to_event( $event1, $attendee12 );
		$this->adder->add_to_event( $event1, $attendee13 );

		$this->assertTrue( $attendee11->is_new_contributor_legacy() );
		$this->assertTrue( $attendee12->is_new_contributor_legacy() );
		$this->assertFalse( $attendee13->is_new_contributor_legacy() );

		$event2_id  = $this->event_factory->create_active( array(), $now->modify( '-1 day' ) );
		$event2     = $this->event_repository->get_event( $event2_id );
		$attendee21 = new Attendee( $event2_id, $user1_id );
		$attendee22 = new Attendee( $event2_id, $user2_id );
		$attendee23 = new Attendee( $event2_id, $user3_id );
		$this->adder->add_to_event( $event2, $attendee21 );
		$this->adder->add_to_event( $event2, $attendee22 );
		$this->adder->add_to_event( $event2, $attendee23 );
		$this->assertTrue( $attendee21->is_new_contributor_legacy() );
		$this->assertTrue( $attendee22->is_new_contributor_legacy() );
		$this->assertTrue( $attendee23->is_new_contributor_legacy() );
	}

	public function test_import_stats_if_active_event() {
		$this->set_normal_user_as_current();
		$user_id = get_current_user_id();

		// Create a translation before the event starts, which should not be imported.
		$this->translation_factory->create( $user_id, new DateTimeImmutable( '1 day ago', new DateTimeZone( 'UTC' ) ) );

		$event_id = $this->event_factory->create_active( array(), new DateTimeImmutable( '5 minutes ago', new DateTimeZone( 'UTC' ) ) );
		$event    = $this->event_repository->get_event( $event_id );
		$attendee = new Attendee( $event_id, $user_id );

		// Create translations while the event is active.
		$translation1 = $this->translation_factory->create( $user_id );
		$translation2 = $this->translation_factory->create( $user_id );

		/** @var GP_Translation $translation_rejected  */
		/** @var GP_Translation $translation_old  */
		$translation_rejected = $this->translation_factory->create( $user_id );
		$translation_old      = $this->translation_factory->create( $user_id );

		$translation_rejected->update( array( 'status' => 'rejected' ) );
		$translation_old->update( array( 'status' => 'old' ) );

		// Make sure no stats were created yet.
		$this->assertEquals( 0, $this->stats_factory->get_count() );

		$this->adder->add_to_event( $event, $attendee );

		$stats = $this->stats_factory->get_by_event_id( $event_id );
		$this->assertCount( 2, $stats );

		$stats1 = $stats[0];
		$this->assertEquals( $event_id, $stats1['event_id'] );
		$this->assertEquals( $user_id, $stats1['user_id'] );
		$this->assertEquals( $translation1->original_id, $stats1['original_id'] );
		$this->assertEquals( 'create', $stats1['action'] );
		$this->assertEquals( 'aa', $stats1['locale'] );
		$this->assertEquals( $translation1->date_added, $stats1['happened_at'] );

		$stats2 = $stats[1];
		$this->assertEquals( $event_id, $stats2['event_id'] );
		$this->assertEquals( $user_id, $stats2['user_id'] );
		$this->assertEquals( $translation2->original_id, $stats2['original_id'] );
		$this->assertEquals( 'create', $stats2['action'] );
		$this->assertEquals( 'aa', $stats2['locale'] );
		$this->assertEquals( $translation2->date_added, $stats1['happened_at'] );
	}

	public function test_does_not_import_stats_if_inactive_event() {
		$user_id  = get_current_user_id();
		$event_id = $this->event_factory->create_inactive_future();
		$event    = $this->event_repository->get_event( $event_id );
		$attendee = new Attendee( $event_id, $user_id );

		$this->adder->add_to_event( $event, $attendee );

		$stats = $this->stats_factory->get_by_event_id( $event_id );
		$this->assertEmpty( $stats );
	}
}
