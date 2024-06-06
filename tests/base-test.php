<?php

namespace Wporg\Tests;

use DateTimeImmutable;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Translation_Events;

abstract class Base_Test extends GP_UnitTestCase {
	protected DateTimeImmutable $now;

	public function setUp(): void {
		parent::setUp();
		$this->now = Translation_Events::now();
	}
}
