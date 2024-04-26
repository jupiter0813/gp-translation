<?php

namespace Wporg\TranslationEvents\Routes\Attendee;

use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Stats\Stats_Calculator;


/**
 * Displays the event list page.
 */
class List_Route extends Route {
	private Attendee_Repository $attendee_repository;
	private Stats_Calculator $stats_calculator;



	public function __construct() {
		parent::__construct();

		$this->attendee_repository = new Attendee_Repository();
		$this->stats_calculator    = new Stats_Calculator();
	}

	public function handle( int $event_id ): void {
		$attendees = array();
		if ( gp_get( 'filter' ) && 'hosts' !== gp_get( 'filter' ) ) {
			$filtered_result = $this->filter_attendees( gp_get( 'filter' ), $event_id );
			if ( $filtered_result ) {
				$attendees = array();
				foreach ( $filtered_result as $row ) {
					$attendee = $this->attendee_repository->get_attendee( $event_id, intval( $row->ID ) );
					if ( '1' === $attendee->is_host() ) {
						$attendee->mark_as_host();
					}
					$attendees[] = $attendee;
				}
			}
		} elseif ( gp_get( 'filter' ) && 'hosts' === gp_get( 'filter' ) ) {
			$attendees = $this->attendee_repository->get_hosts( $event_id );
		} else {
			$attendees = $this->attendee_repository->get_attendees( $event_id );
		}

		$url = gp_url( 'events/' . $event_id . '/attendees' );
		$this->tmpl( 'events-attendees', get_defined_vars() );
	}

	private function filter_attendees( $filter, $event_id ) {
		$this->attendee_repository = new Attendee_Repository();
		switch ( $filter ) {
			case 'hosts':
				return $this->attendee_repository->get_hosts( $event_id );
			case 'contributors':
				return $this->stats_calculator->get_contributors( $event_id );
			case 'new_contributors':
				// todo.
				return array();
			case 'non-contributors':
				$this->stats_calculator->get_attendees_not_contributing( $event_id );
		}
	}
}
