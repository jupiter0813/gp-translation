<?php

namespace Wporg\Tests\Stats;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Stats\Stats_Importer;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;
use Wporg\TranslationEvents\Tests\Translation_Factory;

class Stats_Importer_Test extends GP_UnitTestCase {
	private Translation_Factory $translation_factory;
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Stats_Importer $importer;

	public function setUp(): void {
		parent::setUp();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
		$this->importer            = new Stats_Importer();
	}

	public function test_import_for_user_and_event() {
		// TODO.
		$this->markTestSkipped();
	}
}
