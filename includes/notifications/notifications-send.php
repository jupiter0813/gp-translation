<?php

namespace Wporg\TranslationEvents\Notifications;

use DateTimeZone;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use WP_User;

class Notifications_Send {

	private Attendee_Repository $attendee_repository;
	private Event_Repository_Interface $event_repository;

	/**
	 * Notifications_Cron constructor.
	 *
	 * @param Event_Repository_Interface $event_repository    Event repository.
	 * @param Attendee_Repository        $attendee_repository Attendee repository.
	 */
	public function __construct(
		Event_Repository_Interface $event_repository,
		Attendee_Repository $attendee_repository
	) {
		$this->event_repository    = $event_repository;
		$this->attendee_repository = $attendee_repository;
		add_action( 'wporg_gp_translation_events_email_notifications_1h', array( $this, 'send_notification' ), 10, 1 );
		add_action( 'wporg_gp_translation_events_email_notifications_24h', array( $this, 'send_notification' ), 10, 1 );
	}

	/**
	 * Send notifications to the attendees of the event.
	 *
	 * @param array $args The arguments.
	 *
	 * @return void
	 */
	public function send_notification( array $args ) {
		$hours_before = 1;
		if ( 'wporg_gp_translation_events_email_notifications_24h' === current_filter() ) {
			$hours_before = 24;
		} elseif ( 'wporg_gp_translation_events_email_notifications_1h' !== current_filter() ) {
			return;
		}
		$event     = $this->event_repository->get_event( $args['post_id'] );
		$attendees = $this->attendee_repository->get_attendees( $event->id() );
		foreach ( $attendees as $attendee ) {
			$this->send_email_notification( $event, $attendee, $hours_before );
		}
	}

	/**
	 * Send an email notification to the attendee of the event.
	 *
	 * @param Event    $event        The event.
	 * @param Attendee $attendee     The attendee.
	 * @param int      $hours_before The number of hours before the event starts.
	 *
	 * @return void
	 */
	public function send_email_notification( Event $event, Attendee $attendee, int $hours_before ): void {
		$user    = get_user_by( 'ID', $attendee->user_id() );
		$subject = $this->get_email_subject( $event, $hours_before );
		$message = $this->get_email_message( $user, $event, $hours_before );
		wp_mail(
			$user->user_email,
			$subject,
			$message,
			'Content-Type: text/html'
		);
	}

	/**
	 * Get the email subject.
	 *
	 * @param Event $event        The event.
	 * @param int   $hours_before The number of hours before the event starts.
	 *
	 * @return string
	 */
	private function get_email_subject( Event $event, int $hours_before ): string {
		$number_of_days = intval( $hours_before / 24 );

		$subject = esc_html__( 'Event Reminder. ', 'gp-translation-events' );
		// translators: %s: Event title.
		$subject .= sprintf( esc_html__( 'You have the %s event in', 'gp-translation-events' ), esc_html( $event->title() ) );
		$subject .= ' ';
		if ( $number_of_days >= 1 ) {
			// translators: %s: Number of days.
			$subject .= sprintf( _n( '%s day', '%s days', $number_of_days, 'gp-translation-events' ), $number_of_days );
		} else {
			// translators: %s: Number of hours.
			$subject .= sprintf( _n( '%s hour', '%s hours', $hours_before, 'gp-translation-events' ), $hours_before );
		}

		return $subject;
	}

	/**
	 * Get the email message.
	 *
	 * @param WP_User $user         The user.
	 * @param Event   $event        The event.
	 * @param int     $hours_before The number of hours before the event starts.
	 *
	 * @return string
	 */
	private function get_email_message( WP_User $user, Event $event, int $hours_before ): string {
		$number_of_days = intval( $hours_before / 24 );

		// translators: %s: User display name.
		$message  = sprintf( esc_html__( 'Hi %s', 'gp-translation-events' ), $user->display_name );
		$message .= '<br><br>';
		// translators: %s: Event title.
		$message .= sprintf( esc_html__( 'You have the %s event in', 'gp-translation-events' ), esc_html( $event->title() ) );
		$message .= ' ';
		if ( $number_of_days >= 1 ) {
			// translators: %s: Number of days.
			$message .= sprintf( _n( '%s day.', '%s days.', $number_of_days, 'gp-translation-events' ), $number_of_days );
		} else {
			// translators: %s: Number of hours.
			$message .= sprintf( _n( '%s hour.', '%s hours.', $hours_before, 'gp-translation-events' ), $hours_before );
		}
		$message         .= '<br>';
		$local_start_date = $event->start()->setTimezone( new DateTimeZone( $event->timezone()->getName() ) );
		// translators: %s: Event start date in 'Y-m-d H:i' format.
		$message .= sprintf( esc_html__( 'The event will start at %s', 'gp-translation-events' ), $local_start_date->format( 'Y-m-d H:i' ) );
		$message .= ' ';
		// translators: %s: Event timezone name.
		$message .= sprintf( esc_html__( '(%s local time).', 'gp-translation-events' ), $local_start_date->getTimezone()->getName() );
		$message .= '<br><br>';
		$message .= sprintf(
			wp_kses(
			// translators: %s: Event permalink.
				__( 'You can get more info about the event or stop attending the event <a href="%s">in this link</a>.', 'gp-translation-events' ),
				array( 'a' => array( 'href' => array() ) )
			),
			esc_url( home_url( gp_url( wp_make_link_relative( get_the_permalink( $event->id() ) ) ) ) )
		);
		$message .= '<br><br>';
		$message .= esc_html__( 'Have a nice day', 'gp-translation-events' );

		return $message;
	}
}
