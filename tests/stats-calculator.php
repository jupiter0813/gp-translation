<?php

namespace Wporg\Tests;

use WP_UnitTestCase;
use Wporg\TranslationEvents\Stats_Calculator;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;

class Stats_Calculator_Test extends WP_UnitTestCase {
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Stats_Calculator $calculator;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->stats_factory = new Stats_Factory();
		$this->calculator    = new Stats_Calculator();
	}

	private function test_tells_whether_event_has_stats() {
		$this->markTestSkipped( 'TODO' );
	}
}
