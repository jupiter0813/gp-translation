<?php

namespace Wporg\TranslationEvents\Event;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GP;
use Wporg\TranslationEvents\Active_Events_Cache;
use Wporg\TranslationEvents\Stats_Calculator;
use Wporg\TranslationEvents\Translation_Events;

class Event_Form_Handler {
	public function handle( array $form_data ): void {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'The user must be logged in.', 'gp-translation-events' ), 403 );
		}
		$action           = isset( $form_data['form_name'] ) ? sanitize_text_field( wp_unslash( $form_data['form_name'] ) ) : '';
		$response_message = '';
		$is_nonce_valid   = false;
		$nonce_name       = '_event_nonce';
		if ( ! in_array( $action, array( 'create_event', 'edit_event', 'delete_event' ), true ) ) {
			wp_send_json_error( esc_html__( 'Invalid form name.', 'gp-translation-events' ), 403 );
		}
		/**
		 * Filter the ability to create, edit, or delete an event.
		 *
		 * @param bool $can_crud_event Whether the user can create, edit, or delete an event.
		 */
		$can_crud_event = apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->current_user_can( 'admin' ) );
		if ( 'create_event' === $action && ( ! $can_crud_event ) ) {
			wp_send_json_error( esc_html__( 'The user does not have permission to create an event.', 'gp-translation-events' ), 403 );
		}
		if ( 'edit_event' === $action ) {
			$event_id = isset( $form_data['event_id'] ) ? sanitize_text_field( wp_unslash( $form_data['event_id'] ) ) : '';
			$event    = get_post( $event_id );
			if ( ! ( $can_crud_event || current_user_can( 'edit_post', $event_id ) || intval( $event->post_author ) === get_current_user_id() ) ) {
				wp_send_json_error( esc_html__( 'The user does not have permission to edit or delete the event.', 'gp-translation-events' ), 403 );
			}
		}
		if ( 'delete_event' === $action ) {
			$event_id = isset( $form_data['event_id'] ) ? sanitize_text_field( wp_unslash( $form_data['event_id'] ) ) : '';
			$event    = get_post( $event_id );
			if ( ! ( $can_crud_event || current_user_can( 'delete_post', $event->ID ) || get_current_user_id() === $event->post_author ) ) {
				wp_send_json_error( esc_html__( 'You do not have permission to delete this event.', 'gp-translation-events' ), 403 );
			}
		}
		if ( isset( $form_data[ $nonce_name ] ) ) {
			$nonce_value = sanitize_text_field( wp_unslash( $form_data[ $nonce_name ] ) );
			if ( wp_verify_nonce( $nonce_value, $nonce_name ) ) {
				$is_nonce_valid = true;
			}
		}
		if ( ! $is_nonce_valid ) {
			wp_send_json_error( esc_html__( 'Nonce verification failed.', 'gp-translation-events' ), 403 );
		}

		if ( 'delete_event' === $action ) {
			// Delete event.

			$event_id = sanitize_text_field( wp_unslash( $form_data['event_id'] ) );
			$event    = get_post( $event_id );
			if ( ! $event || Translation_Events::CPT !== $event->post_type ) {
				wp_send_json_error( esc_html__( 'Event does not exist.', 'gp-translation-events' ), 404 );
			}
			if ( ! ( current_user_can( 'delete_post', $event->ID ) || get_current_user_id() === $event->post_author ) ) {
				wp_send_json_error( 'You do not have permission to delete this event' );
			}
			$stats_calculator = new Stats_Calculator();
			try {
				$event_stats = $stats_calculator->for_event( $event );
			} catch ( Exception $e ) {
				wp_send_json_error( esc_html__( 'Failed to calculate event stats.', 'gp-translation-events' ), 500 );
			}
			if ( ! empty( $event_stats->rows() ) ) {
				wp_send_json_error( esc_html__( 'Event has translations and cannot be deleted.', 'gp-translation-events' ), 422 );
			}
			wp_trash_post( $event_id );
			$response_message = esc_html__( 'Event deleted successfully!', 'gp-translation-events' );
			$event_status     = 'deleted';
		} else {
			// Create or update event.

			try {
				$new_event = $this->parse_form_data( $form_data );
			} catch ( InvalidTimeZone $e ) {
				wp_send_json_error( esc_html__( 'Invalid time zone.', 'gp-translation-events' ), 422 );
				return;
			} catch ( InvalidStart $e ) {
				wp_send_json_error( esc_html__( 'Invalid start date.', 'gp-translation-events' ), 422 );
				return;
			} catch ( InvalidEnd $e ) {
				wp_send_json_error( esc_html__( 'Invalid end date.', 'gp-translation-events' ), 422 );
				return;
			} catch ( InvalidStatus $e ) {
				wp_send_json_error( esc_html__( 'Invalid status.', 'gp-translation-events' ), 422 );
				return;
			} catch ( InvalidTitle $e ) {
				wp_send_json_error( esc_html__( 'Invalid title.', 'gp-translation-events' ), 422 );
				return;
			}
			if ( $new_event->end() < new DateTime( 'now', new DateTimeZone( 'UTC' ) ) ) {
				wp_send_json_error( esc_html__( 'Past events cannot be created or edited.', 'gp-translation-events' ), 422 );
				return;
			}

			// This is a list of slugs that are not allowed, as they conflict with the event URLs.
			$invalid_slugs = array( 'new', 'edit', 'attend', 'my-events' );
			if ( in_array( sanitize_title( $new_event->title() ), $invalid_slugs, true ) ) {
				wp_send_json_error( esc_html__( 'Invalid slug.', 'gp-translation-events' ), 422 );
			}

			if ( 'create_event' === $action ) {
				$event_id         = wp_insert_post(
					array(
						'post_type'    => Translation_Events::CPT,
						'post_title'   => $new_event->title(),
						'post_content' => $new_event->description(),
						'post_status'  => $new_event->status(),
					)
				);
				$response_message = esc_html__( 'Event created successfully!', 'gp-translation-events' );
			}
			if ( 'edit_event' === $action ) {
				if ( ! isset( $form_data['event_id'] ) ) {
					wp_send_json_error( esc_html__( 'Event id is required.', 'gp-translation-events' ), 422 );
				}
				$event_id = sanitize_text_field( wp_unslash( $form_data['event_id'] ) );
				$event    = get_post( $event_id );
				if ( ! $event || Translation_Events::CPT !== $event->post_type || ! ( current_user_can( 'edit_post', $event->ID ) || intval( $event->post_author ) === get_current_user_id() ) ) {
					wp_send_json_error( esc_html__( 'Event does not exist.', 'gp-translation-events' ), 404 );
				}
				wp_update_post(
					array(
						'ID'           => $event_id,
						'post_title'   => $new_event->title(),
						'post_content' => $new_event->description(),
						'post_status'  => $new_event->status(),
					)
				);
				$response_message = esc_html__( 'Event updated successfully!', 'gp-translation-events' );
			}
			if ( ! $event_id ) {
				wp_send_json_error( esc_html__( 'Event could not be created or updated.', 'gp-translation-events' ), 422 );
			}
			try {
				update_post_meta( $event_id, '_event_start', $new_event->start()->format( 'Y-m-d H:i:s' ) );
				update_post_meta( $event_id, '_event_end', $new_event->end()->format( 'Y-m-d H:i:s' ) );
			} catch ( Exception $e ) {
				wp_send_json_error( esc_html__( 'Invalid start or end', 'gp-translation-events' ), 422 );
			}
			update_post_meta( $event_id, '_event_timezone', $new_event->timezone()->getName() );

			$event_status = $new_event->status();
		}

		try {
			Active_Events_Cache::invalidate();
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e );
		}

		list( $permalink, $post_name ) = get_sample_permalink( $event_id );
		$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
		wp_send_json_success(
			array(
				'message'        => $response_message,
				'eventId'        => $event_id,
				'eventUrl'       => str_replace( '%pagename%', $post_name, $permalink ),
				'eventStatus'    => $event_status,
				'eventEditUrl'   => esc_url( gp_url( '/events/edit/' . $event_id ) ),
				'eventDeleteUrl' => esc_url( gp_url( '/events/my-events/' ) ),
			)
		);
	}

	// PHPCS erroneously thinks there should be only two throw tags.
	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
	/**
	 * @throws InvalidStart
	 * @throws InvalidEnd
	 * @throws InvalidTimeZone
	 * @throws InvalidTitle
	 * @throws InvalidStatus
	 */
	// phpcs:enable
	private function parse_form_data( array $data ): Event {
		$event_id = isset( $data['event_id'] ) ? sanitize_text_field( wp_unslash( $data['event_id'] ) ) : 0;
		$title    = isset( $data['event_title'] ) ? sanitize_text_field( wp_unslash( $data['event_title'] ) ) : '';

		// This will be sanitized by sanitize_post which is called in wp_insert_post.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$description    = isset( $data['event_description'] ) ? force_balance_tags( wp_unslash( $data['event_description'] ) ) : '';
		$event_start    = isset( $data['event_start'] ) ? sanitize_text_field( wp_unslash( $data['event_start'] ) ) : '';
		$event_end      = isset( $data['event_end'] ) ? sanitize_text_field( wp_unslash( $data['event_end'] ) ) : '';
		$event_timezone = isset( $data['event_timezone'] ) ? sanitize_text_field( wp_unslash( $data['event_timezone'] ) ) : '';

		$event_status = '';
		if ( isset( $data['event_form_action'] ) && in_array( $data['event_form_action'], array( 'draft', 'publish', 'delete' ), true ) ) {
			$event_status = sanitize_text_field( wp_unslash( $data['event_form_action'] ) );
		}

		try {
			$timezone = new DateTimeZone( $event_timezone );
		} catch ( Exception $e ) {
			throw new InvalidTimeZone();
		}

		try {
			$start = new DateTimeImmutable( $event_start, $timezone );
		} catch ( Exception $e ) {
			throw new InvalidStart();
		}

		try {
			$end = new DateTimeImmutable( $event_end, $timezone );
		} catch ( Exception $e ) {
			throw new InvalidEnd();
		}

		return new Event(
			intval( $event_id ),
			get_current_user_id(),
			$start->setTimezone( new DateTimeZone( 'UTC' ) ),
			$end->setTimezone( new DateTimeZone( 'UTC' ) ),
			$timezone,
			sanitize_title( $title ),
			$event_status,
			$title,
			$description,
		);
	}
}
