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
		if ( ! $event || 'event' !== $event->post_type ) {
			$this->die_with_404();
		}
		$this->tmpl( 'events-edit', get_defined_vars() );
	}
}
