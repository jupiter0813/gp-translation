<?php

namespace Wporg\TranslationEvents\Routes\Event;

use DateTime;
use DateTimeZone;
use Exception;
use WP_Query;
use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Translation_Events;

/**
 * Displays the event list page.
 */
class List_Route extends Route {
	private Attendee_Repository $attendee_repository;

	public function __construct() {
		parent::__construct();
		$this->attendee_repository = new Attendee_Repository();
	}

	public function handle(): void {
		$current_datetime_utc = null;
		try {
			$current_datetime_utc = ( new DateTime( 'now', new DateTimeZone( 'UTC' ) ) )->format( 'Y-m-d H:i:s' );
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e );
			$this->die_with_error( esc_html__( 'Something is wrong.', 'gp-translation-events' ) );
		}

		$_current_events_paged        = 1;
		$_upcoming_events_paged       = 1;
		$_past_events_paged           = 1;
		$_user_attending_events_paged = 1;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['current_events_paged'] ) ) {
			$value = sanitize_text_field( wp_unslash( $_GET['current_events_paged'] ) );
			if ( is_numeric( $value ) ) {
				$_current_events_paged = (int) $value;
			}
		}
		if ( isset( $_GET['upcoming_events_paged'] ) ) {
			$value = sanitize_text_field( wp_unslash( $_GET['upcoming_events_paged'] ) );
			if ( is_numeric( $value ) ) {
				$_upcoming_events_paged = (int) $value;
			}
		}
		if ( isset( $_GET['past_events_paged'] ) ) {
			$value = sanitize_text_field( wp_unslash( $_GET['past_events_paged'] ) );
			if ( is_numeric( $value ) ) {
				$_past_events_paged = (int) $value;
			}
		}
		if ( isset( $_GET['user_attending_events_paged'] ) ) {
			$value = sanitize_text_field( wp_unslash( $_GET['user_attending_events_paged'] ) );
			if ( is_numeric( $value ) ) {
				$_user_attending_events_paged = (int) $value;
			}
		}
		// phpcs:enable

		$current_events_args  = array(
			'post_type'      => Translation_Events::CPT,
			'posts_per_page' => 10,
			'paged'          => $_current_events_paged,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'     => array(
				array(
					'key'     => '_event_start',
					'value'   => $current_datetime_utc,
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
				array(
					'key'     => '_event_end',
					'value'   => $current_datetime_utc,
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);
		$current_events_query = new WP_Query( $current_events_args );

		$upcoming_events_args  = array(
			'post_type'      => Translation_Events::CPT,
			'posts_per_page' => 10,
			'paged'          => $_upcoming_events_paged,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'     => array(
				array(
					'key'     => '_event_start',
					'value'   => $current_datetime_utc,
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);
		$upcoming_events_query = new WP_Query( $upcoming_events_args );

		$past_events_args  = array(
			'post_type'      => Translation_Events::CPT,
			'posts_per_page' => 10,
			'paged'          => $_past_events_paged,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'     => array(
				array(
					'key'     => '_event_end',
					'value'   => $current_datetime_utc,
					'compare' => '<',
					'type'    => 'DATETIME',
				),
			),
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);
		$past_events_query = new WP_Query( $past_events_args );

		$user_attending_events_args = array(
			'post_type'      => Translation_Events::CPT,
			'posts_per_page' => 10,
			'paged'          => $_user_attending_events_paged,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'     => array(
				array(
					'key'     => '_event_end',
					'value'   => $current_datetime_utc,
					'compare' => '>',
					'type'    => 'DATETIME',
				),
			),
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'       => '_event_start',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);

		$user_attending_event_ids = $this->attendee_repository->get_events_for_user( get_current_user_id() );
		if ( empty( $user_attending_event_ids ) ) {
			// Setting it to an array with a single 0 element will result in the query returning zero results,
			// which is what we want, as the user is not attending any events.
			$user_attending_event_ids = array( 0 );
		}
		$user_attending_events_args['post__in'] = $user_attending_event_ids;

		$user_attending_events_query = new WP_Query( $user_attending_events_args );
		$this->tmpl( 'events-list', get_defined_vars() );
	}
}
