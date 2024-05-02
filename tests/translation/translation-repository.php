<?php

namespace translation;

use DateTimeImmutable;
use DateTimeZone;
use GP_UnitTestCase;
use Wporg\TranslationEvents\Tests\Translation_Factory;
use Wporg\TranslationEvents\Translation\Translation_Repository;

class Translation_Repository_Test extends GP_UnitTestCase {
	private Translation_Factory $translation_factory;
	private Translation_Repository $repository;

	public function setUp(): void {
		parent::setUp();
		$this->translation_factory = new Translation_Factory( $this->factory );
		$this->repository          = new Translation_Repository();
	}

	public function test_count_translations_before() {
		// TODO.
		$this->markTestSkipped();
	}
}
