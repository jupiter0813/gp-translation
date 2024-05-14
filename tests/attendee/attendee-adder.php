<?php
namespace Wporg\Tests\Attendee;

use GP_UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Adder;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Stats\Stats_Importer;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Attendee_Adder_Test extends GP_UnitTestCase {
	/**
	 * @var MockObject|Attendee_Repository
	 */
	private $attendee_repository;

	/**
	 * @var MockObject|Stats_Importer
	 */
	private $stats_importer;

	private Attendee_Adder $adder;
	private Event_Repository $event_repository;
	private Event_Factory $event_factory;

	public function setUp(): void {
		parent::setUp();
		$this->attendee_repository = $this->createMock( Attendee_Repository::class );
		$this->stats_importer      = $this->createMock( Stats_Importer::class );
		$this->adder               = new Attendee_Adder( $this->attendee_repository, $this->stats_importer );
		$this->event_repository    = new Event_Repository( new Attendee_Repository() );
		$this->event_factory       = new Event_Factory();

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

	public function test_import_stats_if_active_event() {
		$user_id  = get_current_user_id();
		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );
		$attendee = new Attendee( $event_id, $user_id );

		$this->stats_importer
			->expects( $this->once() )
			->method( 'import_for_user_and_event' )
			->with( $this->equalTo( $user_id, $event ) );

		$this->adder->add_to_event( $event, $attendee );
	}

	public function test_does_not_import_stats_if_inactive_event() {
		$user_id  = get_current_user_id();
		$event_id = $this->event_factory->create_inactive_future();
		$event    = $this->event_repository->get_event( $event_id );
		$attendee = new Attendee( $event_id, $user_id );

		$this->stats_importer
			->expects( $this->never() )
			->method( 'import_for_user_and_event' );

		$this->adder->add_to_event( $event, $attendee );
	}
}
