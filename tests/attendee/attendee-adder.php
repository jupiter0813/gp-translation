<?php
namespace Wporg\Tests\Attendee;

use PHPUnit\Framework\MockObject\MockObject;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee_Adder;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Stats\Stats_Importer;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Attendee_Adder_Test extends WP_UnitTestCase {
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

	protected function setUp(): void {
		parent::setUp();
		$this->attendee_repository = $this->createMock( Attendee_Repository::class );
		$this->stats_importer      = $this->createMock( Stats_Importer::class );
		$this->adder               = new Attendee_Adder( $this->attendee_repository, $this->stats_importer );
		$this->event_repository    = new Event_Repository( new Attendee_Repository() );
		$this->event_factory       = new Event_Factory();
	}
}
