<?php

namespace Wporg\TranslationEvents\Routes\Attendee;

use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;


/**
 * Displays the event list page.
 */
class List_Route extends Route {
	private Attendee_Repository $attendee_repository;


	public function handle( int $event_id ): void {
		$this->attendee_repository = new Attendee_Repository();
		$attendees                 = $this->attendee_repository->get_attendees( $event_id );
		$this->tmpl( 'events-attendees', get_defined_vars() );
	}
}
