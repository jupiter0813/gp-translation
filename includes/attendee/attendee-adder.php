<?php

namespace Wporg\TranslationEvents\Attendee;

use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Stats\Stats_Importer;

class Attendee_Adder {
	private Attendee_Repository $attendee_repository;
	private Stats_Importer $stats_importer;

	public function __construct( Attendee_Repository $attendee_repository, Stats_Importer $stats_importer ) {
		$this->attendee_repository = $attendee_repository;
		$this->stats_importer      = $stats_importer;
	}

	/**
	 * Add an attendee to an event.
	 *
	 * @param Event    $event    Event to which to add the attendee.
	 * @param Attendee $attendee Attendee to add to the event.
	 */
	public function add_to_event( Event $event, Attendee $attendee ): void {
		// TODO.
	}
}
