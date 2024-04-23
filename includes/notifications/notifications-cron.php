<?php

namespace Wporg\TranslationEvents\Notifications;

use DateTimeImmutable;
use DateTimeZone;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;

class Notifications_Cron {
	private Attendee_Repository $attendee_repository;
	private Event_Repository_Interface $event_repository;

	/**
	 * Notifications_Cron constructor.
	 *
	 * @param Event_Repository_Interface $event_repository    Event repository.
	 * @param Attendee_Repository        $attendee_repository Attendee repository.
	 */
	public function __construct( Event_Repository_Interface $event_repository, Attendee_Repository $attendee_repository ) {
		$this->event_repository    = $event_repository;
		$this->attendee_repository = $attendee_repository;

		add_action( 'wporg_gp_translation_events_cron_notifications', array( $this, 'handle' ) );
		if ( ! wp_next_scheduled( 'wporg_gp_translation_events_cron_notifications' ) ) {
			wp_schedule_event( time(), 'hourly', 'wporg_gp_translation_events_cron_notifications' );
		}
	}

	/**
	 * Handle the cron job.
	 *
	 * Send notifications to active events.
	 *
	 * @return void
	 */
	public function handle(): void {
		$notifications = new Notifications( $this->event_repository, $this->attendee_repository );
		$notifications->send_notifications_to_active_events();
	}
}
