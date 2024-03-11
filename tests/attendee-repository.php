<?php

namespace Wporg\Tests;

use WP_UnitTestCase;
use Wporg\TranslationEvents\Attendee_Repository;

class Attendee_Repository_Test extends WP_UnitTestCase {
	private Attendee_Repository $repository;

	protected function setUp(): void {
		parent::setUp();
		$this->repository = new Attendee_Repository();
	}
}
