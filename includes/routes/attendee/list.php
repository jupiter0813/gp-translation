<?php

namespace Wporg\TranslationEvents\Routes\Attendee;

use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;


/**
 * Displays the event list page.
 */
class List_Route extends Route {
	private Attendee_Repository $attendee_repository;



	public function __construct() {
		parent::__construct();
		$this->attendee_repository = new Attendee_Repository();
	}

	public function handle( int $event_id ): void {
		global $wp;

		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wp_login_url( home_url( $wp->request ) ) );
			exit;
		}
		if ( ! current_user_can( 'edit_translation_event', $event_id ) ) {
			$this->die_with_error( esc_html__( 'You do not have permission to edit this event.', 'gp-translation-events' ), 403 );
		}
		$attendees = array();

		if ( gp_get( 'filter' ) && 'hosts' == gp_get( 'filter' ) ) {
			$attendees = $this->attendee_repository->get_hosts( $event_id );
		} else {
			$attendees = $this->attendee_repository->get_attendees( $event_id );
		}

		$this->tmpl( 'events-attendees', get_defined_vars() );
	}
}
