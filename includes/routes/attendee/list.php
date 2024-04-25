<?php

namespace Wporg\TranslationEvents\Routes\Attendee;

use Wporg\TranslationEvents\Routes\Route;


/**
 * Displays the event list page.
 */
class List_Route extends Route {
	public function handle(): void {
		$this->tmpl( 'events-attendees', get_defined_vars() );
	}
}
