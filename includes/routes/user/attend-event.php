<?php

namespace Wporg\TranslationEvents\Routes\User;

use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Routes\Route;

/**
 * Toggle whether the current user is attending an event.
 * If the user is not currently marked as attending, they will be marked as attending.
 * If the user is currently marked as attending, they will be marked as not attending.
 */
class Attend_Event_Route extends Route {
	public function handle( int $event_id ): void {
		$user = wp_get_current_user();
		if ( ! $user ) {
			$this->die_with_error( esc_html__( 'Only logged-in users can attend events', 'gp-translation-events' ), 403 );
		}

		$event = get_post( $event_id );

		if ( ! $event ) {
			$this->die_with_404();
		}

		$event_ids = get_user_meta( $user->ID, Attendee_Repository::USER_META_KEY, true ) ?? array();
		if ( ! $event_ids ) {
			$event_ids = array();
		}

		if ( ! isset( $event_ids[ $event_id ] ) ) {
			// Not yet attending, mark as attending.
			$event_ids[ $event_id ] = true;
		} else {
			// Currently attending, mark as not attending.
			unset( $event_ids[ $event_id ] );
		}

		update_user_meta( $user->ID, Attendee_Repository::USER_META_KEY, $event_ids );

		wp_safe_redirect( gp_url( "/events/$event->post_name" ) );
		exit;
	}
}
