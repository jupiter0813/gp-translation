<?php

namespace Wporg\TranslationEvents\Attendee;

use Exception;
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
	 *
	 * @throws Exception
	 */
	public function add_to_event( Event $event, Attendee $attendee ): void {
		$this->attendee_repository->insert_attendee( $attendee );

		// If the event is active right now,
		// import stats for translations the user created since the event started.
		if ( $event->is_active() ) {
			$this->stats_importer->import_for_user_and_event( $attendee->user_id(), $event );
		}
	}
}
