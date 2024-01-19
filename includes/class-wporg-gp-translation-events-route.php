<?php
/**
 * Routes: WPORG_GP_Translation_Events_Route class
 *
 * @package wporg-gp-translation-events
 */
class WPORG_GP_Translation_Events_Route extends GP_Route {


	/**
	 * WPORG_GP_Translation_Events_Route constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->template_path = dirname( __FILE__ ) . '/../templates/';
	}

	/**
	 * Loads the 'events_list' template.
	 *
	 * @return void
	 */
	public function events_list() {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$this->tmpl( 'events-list', get_defined_vars() );
	}

	/**
	 * Loads the 'events_create' template.
	 *
	 * @return void
	 */
	public function events_create() {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$this->tmpl( 'events-create' );
	}

	/**
	 * Loads the 'events_edit' template.
	 *
	 * @param int $event_id The event ID.
	 * @return void
	 */
	public function events_edit( $event_id ) {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$event = get_post( $event_id );
		if ( ! $event || 'event' !== $event->post_type || ! current_user_can( 'edit_post', $event_id ) ) {
			$this->die_with_404();
		}

		$event_title        = $event->post_title;
		$event_description  = $event->post_content;
		$event_start_date   = get_post_meta( $event_id, '_event_start_date', true ) ?? '';
		$event_end_date     = get_post_meta( $event_id, '_event_end_date', true ) ?? '';
		$event_locale       = get_post_meta( $event_id, '_event_locale', true ) ?? '';
		$event_project_name = get_post_meta( $event_id, '_event_project_name', true ) ?? '';
		$event_timezone     = get_post_meta( $event_id, '_event_timezone', true ) ?? '';
		$this->tmpl( 'events-edit', get_defined_vars() );
	}

	/**
	 * Loads the 'event' template.
	 *
	 * @param string $event_slug The event slug.
	 * @return void
	 */
	public function events_details( $event_slug ) {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$event = get_page_by_path( $event_slug, OBJECT, 'event' );

		if ( ! $event ) {
			$this->die_with_404();
		}
		$event_title        = $event->post_title;
		$event_description  = $event->post_content;
		$event_start_date   = get_post_meta( $event->ID, '_event_start_date', true ) ?? '';
		$event_end_date     = get_post_meta( $event->ID, '_event_end_date', true ) ?? '';
		$event_locale       = get_post_meta( $event->ID, '_event_locale', true ) ?? '';
		$event_project_name = get_post_meta( $event->ID, '_event_project_name', true ) ?? '';
		$this->tmpl( 'event', get_defined_vars() );
	}
}
