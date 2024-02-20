<?php

namespace Wporg\Tests;

use GP_UnitTestCase_Route;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Stats_Listener_Test extends GP_UnitTestCase_Route {
	public $route_class = 'GP_Route_Translation';

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
	}

	public function test_stores_translation_created() {
		$this->assertTrue( true );
	}
}
