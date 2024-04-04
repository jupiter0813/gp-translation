<?php

namespace Wporg\TranslationEvents\Routes\User;

use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;

/**
 * Toggle whether the current user is attending an event.
 * If the user is not currently marked as attending, they will be marked as attending.
 * If the user is currently marked as attending, they will be marked as not attending.
 */
class Attend_Event_Route extends Route {
	private Attendee_Repository $attendee_repository;

	public function __construct() {
		parent::__construct();
		$this->attendee_repository = new Attendee_Repository();
	}

	public function handle( int $event_id ): void {
		$user = wp_get_current_user();
		if ( ! $user ) {
			$this->die_with_error( esc_html__( 'Only logged-in users can attend events', 'gp-translation-events' ), 403 );
		}

		$event = get_post( $event_id );
		if ( ! $event ) {
			$this->die_with_404();
		}

		if ( $this->attendee_repository->is_attending( $event_id, $user->ID ) ) {
			$this->attendee_repository->remove_attendee( $event_id, $user->ID );
		} else {
			$this->attendee_repository->add_attendee( $event_id, $user->ID );
		}

		wp_safe_redirect( gp_url( "/events/$event->post_name" ) );
		exit;
	}
}
