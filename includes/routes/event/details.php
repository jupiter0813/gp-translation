<?php

namespace Wporg\TranslationEvents\Routes\Event;

use DateTime;
use DateTimeZone;
use Exception;
use GP;
use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Stats_Calculator;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Event_Start_Date;
use Wporg\TranslationEvents\Event_End_Date;

/**
 * Displays the event details page.
 */
class Details_Route extends Route {
	private Attendee_Repository $attendee_repository;

	public function __construct() {
		parent::__construct();
		$this->attendee_repository = new Attendee_Repository();
	}

	public function handle( string $event_slug ): void {
		$user  = wp_get_current_user();
		$event = get_page_by_path( $event_slug, OBJECT, Translation_Events::CPT );
		if ( ! $event ) {
			$this->die_with_404();
		}

		/**
		 * Filter the ability to create, edit, or delete an event.
		 *
		 * @param bool $can_crud_event Whether the user can create, edit, or delete an event.
		 */
		$can_crud_event = apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->current_user_can( 'admin' ) );
		if ( 'publish' !== $event->post_status && ! $can_crud_event ) {
			$this->die_with_error( esc_html__( 'You are not authorized to view this page.', 'gp-translation-events' ), 403 );
		}

		$event_id          = $event->ID;
		$event_title       = $event->post_title;
		$event_description = $event->post_content;
		$event_start       = new Event_Start_Date( get_post_meta( $event->ID, '_event_start', true ) ?: '' );
		$event_end         = new Event_End_Date( get_post_meta( $event->ID, '_event_end', true ) ?: '' );
		$user_is_attending = $this->attendee_repository->is_attending( $event_id, $user->ID );

		$stats_calculator = new Stats_Calculator();
		try {
			$event_stats  = $stats_calculator->for_event( $event->ID );
			$contributors = $stats_calculator->get_contributors( $event_id );
			$projects     = $stats_calculator->get_projects( $event_id );
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e );
			$this->die_with_error( esc_html__( 'Failed to calculate event stats', 'gp-translation-events' ) );
		}

		$is_editable_event = true;
		$event_end_utc     = new DateTime( get_post_meta( $event_id, '_event_end', true ), new DateTimeZone( 'UTC' ) );
		$now_utc           = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		if ( $event_end->is_in_the_past() || $stats_calculator->event_has_stats( $event_id ) ) {
			$is_editable_event = false;
		}

		$this->tmpl( 'event', get_defined_vars() );
	}
}
