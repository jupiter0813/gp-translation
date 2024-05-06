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

		$this->set_normal_user_as_current();
	}

	public function test_count_translations_before() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$user1_id = 42;
		$user2_id = 43;
		$user3_id = 44;

		$this->translation_factory->create( $user1_id, $now->modify( '-1 day' ) );
		$this->translation_factory->create( $user1_id, $now->modify( '-1 hour' ) );
		$this->translation_factory->create( $user1_id );
		$this->translation_factory->create( $user1_id, $now->modify( '+1 hour' ) );
		$this->translation_factory->create( $user1_id, $now->modify( '+1 day' ) );

		$this->translation_factory->create( $user2_id, $now->modify( '-1 hour' ) );
		$this->translation_factory->create( $user2_id );
		$this->translation_factory->create( $user2_id, $now->modify( '+1 hour' ) );
		$this->translation_factory->create( $user2_id, $now->modify( '+1 day' ) );

		$counts = $this->repository->count_translations_before( array( $user1_id, $user2_id, $user3_id ), $now );
		$this->assertCount( 3, $counts );
		$this->assertEquals( 2, $counts[ $user1_id ] );
		$this->assertEquals( 1, $counts[ $user2_id ] );
		$this->assertEquals( 0, $counts[ $user3_id ] );
	}
}
