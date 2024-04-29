<?php

namespace Wporg\TranslationEvents\Routes\Event;

use Exception;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Project\Project_Repository;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Stats\Stats_Calculator;
use Wporg\TranslationEvents\Translation_Events;

/**
 * Displays the event details page.
 */
class Details_Route extends Route {
	private Event_Repository_Interface $event_repository;
	private Attendee_Repository $attendee_repository;

	public function __construct() {
		parent::__construct();
		$this->event_repository    = Translation_Events::get_event_repository();
		$this->attendee_repository = Translation_Events::get_attendee_repository();
	}

	public function handle( string $event_slug ): void {
		$user  = wp_get_current_user();
		$event = get_page_by_path( $event_slug, OBJECT, Translation_Events::CPT );
		if ( ! $event ) {
			$this->die_with_404();
		}
		$event = $this->event_repository->get_event( $event->ID );
		if ( ! $event ) {
			$this->die_with_404();
		}

		if ( ! current_user_can( 'view_translation_event', $event->id() ) ) {
			$this->die_with_error( esc_html__( 'You are not authorized to view this page.', 'gp-translation-events' ), 403 );
		}

		$event_id          = $event->id();
		$event_title       = $event->title();
		$event_description = $event->description();
		$event_start       = $event->start();
		$event_end         = $event->end();

		$stats_calculator   = new Stats_Calculator();
		$project_repository = new Project_Repository();

		$attendee          = $this->attendee_repository->get_attendee( $event->id(), $user->ID );
		$user_is_attending = $attendee instanceof Attendee;
		$contributors      = $stats_calculator->get_contributors( $event->id() );
		$attendee_repo     = $this->attendee_repository;
		$hosts             = $this->attendee_repository->get_hosts( $event->id() );
		$attendees         = $this->attendee_repository->get_attendees_not_contributing( $event->id() );
		$projects          = $project_repository->get_for_event( $event->id() );

		try {
			$event_stats = $stats_calculator->for_event( $event->id() );
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e );
			$this->die_with_error( esc_html__( 'Failed to calculate event stats', 'gp-translation-events' ) );
		}

		$this->tmpl( 'event', get_defined_vars() );
	}
}
