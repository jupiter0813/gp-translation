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
		// TODO: must create an active event. This test currently fails because there are no active events.
		$this->event_factory->create();

		$this->set_normal_user_as_current();
		$user_id = wp_get_current_user()->ID;

		$set      = $this->factory->translation_set->create_with_project_and_locale();
		$original = $this->factory->original->create(
			array(
				'project_id' => $set->project->id,
				'status'     => '+active',
				'singular'   => 'foo',
			)
		);

		$translation = $this->factory->translation->create(
			array(
				'user_id'            => $user_id,
				'translation_set_id' => $set->id,
				'original_id'        => $original->id,
				'status'             => 'waiting',
			)
		);

		global $wpdb;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results( 'select * from wp_wporg_gp_translation_events_actions' );
		// phpcs:enable

		// TODO.
		$this->assertNotEmpty( $rows );
	}
}
