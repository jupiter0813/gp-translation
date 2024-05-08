<?php

namespace Wporg\Tests\Event;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;

class Event_Capabilities_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Attendee_Repository $attendee_repository;
	private Event_Repository $event_repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
		$this->attendee_repository = new Attendee_Repository();
		$this->event_repository    = new Event_Repository( $this->attendee_repository );
	}

	public function test_cannot_manage_if_no_crud_permission() {
		$this->set_normal_user_as_current();

		add_filter( 'gp_translation_events_can_crud_event', '__return_false' );

		$this->assertFalse( current_user_can( 'manage_translation_events' ) );
	}

	public function test_can_manage_if_crud_permission() {
		$this->set_normal_user_as_current();
		get_current_user_id();

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );

		$this->assertTrue( current_user_can( 'manage_translation_events' ) );
	}

	public function test_cannot_create_if_no_crud_permission() {
		$this->set_normal_user_as_current();

		add_filter( 'gp_translation_events_can_crud_event', '__return_false' );

		$this->assertFalse( current_user_can( 'create_translation_event' ) );
	}

	public function test_can_create_if_crud_permission() {
		$this->set_normal_user_as_current();
		get_current_user_id();

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );

		$this->assertTrue( current_user_can( 'create_translation_event' ) );
	}

	public function test_cannot_view_non_published_events() {
		$this->set_normal_user_as_current();

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );
		$event->set_status( 'draft' );
		$this->event_repository->update_event( $event );

		$this->assertFalse( current_user_can( 'view_translation_event', $event_id ) );
	}

	public function test_gp_admin_can_view_non_published_events() {
		$this->set_normal_user_as_current();

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );
		$event->set_status( 'draft' );
		$this->event_repository->update_event( $event );

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );

		$this->assertTrue( current_user_can( 'view_translation_event', $event_id ) );
	}

	public function test_event_id_as_string() {
		$this->set_normal_user_as_current();

		$event_id = $this->event_factory->create_active();

		$this->assertTrue( current_user_can( 'edit_translation_event', (string) $event_id ) );
	}

	public function test_author_can_edit() {
		$this->set_normal_user_as_current();

		$event_id = $this->event_factory->create_active();

		$this->assertTrue( current_user_can( 'edit_translation_event', $event_id ) );
	}

	public function test_non_author_cannot_edit() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();

		$this->assertFalse( user_can( $non_author_user_id, 'edit_translation_event', $event_id ) );
	}

	public function test_host_can_edit() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();

		$attendee = new Attendee( $event_id, $non_author_user_id, true );
		$this->attendee_repository->insert_attendee( $attendee );

		$this->assertTrue( user_can( $non_author_user_id, 'edit_translation_event', $event_id ) );
	}

	public function test_gp_admin_can_edit() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();
		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );

		$this->assertTrue( user_can( $non_author_user_id, 'edit_translation_event', $event_id ) );
	}

	public function test_cannot_edit_past_event() {
		$this->set_normal_user_as_current();

		$event_id = $this->event_factory->create_inactive_past();

		$this->assertFalse( current_user_can( 'edit_translation_event', $event_id ) );
	}

	public function test_cannot_edit_event_with_stats() {
		$this->set_normal_user_as_current();
		$author_user_id = get_current_user_id();

		$event_id = $this->event_factory->create_active();
		$this->stats_factory->create( $event_id, $author_user_id, 1, 'create' );

		$this->assertFalse( current_user_can( 'edit_translation_event', $event_id ) );
	}

	public function test_author_can_trash() {
		$this->set_normal_user_as_current();
		$event_id = $this->event_factory->create_active();
		$this->assertTrue( current_user_can( 'trash_translation_event', $event_id ) );
	}

	public function test_non_author_cannot_trash() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();
		$this->assertFalse( user_can( $non_author_user_id, 'trash_translation_event', $event_id ) );
	}

	public function test_host_can_trash() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();

		$attendee = new Attendee( $event_id, $non_author_user_id, true );
		$this->attendee_repository->insert_attendee( $attendee );

		$this->assertTrue( user_can( $non_author_user_id, 'trash_translation_event', $event_id ) );
	}

	public function test_gp_admin_can_trash() {
		$this->set_normal_user_as_current();
		$event_id = $this->event_factory->create_active();
		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );
		$this->assertTrue( current_user_can( 'trash_translation_event', $event_id ) );
	}

	public function test_cannot_delete_if_not_trashed() {
		$this->set_normal_user_as_current();
		$event_id = $this->event_factory->create_active();

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );
		$this->assertFalse( current_user_can( 'delete_translation_event', $event_id ) );
	}

	public function test_non_gp_admin_cannot_delete() {
		$this->set_normal_user_as_current();
		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$event->set_status( 'trash' );
		$this->event_repository->update_event( $event );

		add_filter( 'gp_translation_events_can_crud_event', '__return_false' );
		$this->assertFalse( current_user_can( 'delete_translation_event', $event_id ) );
	}

	public function test_gp_admin_can_delete() {
		$this->set_normal_user_as_current();
		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$event->set_status( 'trash' );
		$this->event_repository->update_event( $event );

		add_filter( 'gp_translation_events_can_crud_event', '__return_true' );
		$this->assertTrue( current_user_can( 'delete_translation_event', $event_id ) );
	}
}
