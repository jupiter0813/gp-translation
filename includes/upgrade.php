<?php

namespace Wporg\TranslationEvents;

use Exception;
use WP_Query;
use Wporg\TranslationEvents\Attendee\Attendee;

class Upgrade {
	public static function upgrade(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_database_schema_sql() );

		// TODO: Remove this once it has been run in production.
		try {
			// Don't run it during tests.
			$is_running_tests = 'yes' === getenv( 'WPORG_TRANSLATION_EVENTS_TESTS' );
			if ( ! $is_running_tests ) {
				self::import_legacy_attendees();
			}
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $e );
		}
	}

	private static function get_database_schema_sql(): string {
		global $gp_table_prefix;

		return "
			CREATE TABLE `{$gp_table_prefix}event_actions` (
				`translate_event_actions_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) NOT NULL COMMENT 'Post_ID of the translation_event post in the wp_posts table',
				`original_id` int(10) NOT NULL COMMENT 'ID of the translation',
				`user_id` int(10) NOT NULL COMMENT 'ID of the user who made the action',
				`action` enum('approve','create','reject','request_changes') NOT NULL COMMENT 'The action that the user made (create, reject, etc)',
				`locale` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Locale of the translation',
				`happened_at` datetime NOT NULL COMMENT 'When the action happened, in UTC',
			PRIMARY KEY (`translate_event_actions_id`),
			UNIQUE KEY `event_per_translated_original_per_user` (`event_id`,`locale`,`original_id`,`user_id`)
			) COMMENT='Tracks translation actions that happened during a translation event';

			CREATE TABLE `{$gp_table_prefix}event_attendees` (
				`translate_event_attendees_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) NOT NULL COMMENT 'Post_ID of the translation_event post in the wp_posts table',
				`user_id` int(10) NOT NULL COMMENT 'ID of the user who is attending the event',
				`is_host` tinyint(1) default 0 not null comment 'Whether the user is a host of the event',
			PRIMARY KEY (`translate_event_attendees_id`),
			UNIQUE KEY `event_per_user` (`event_id`,`user_id`),
			INDEX `user` (`user_id`)
			) COMMENT='Attendees of events';
		";
	}

	/**
	 * Previously, event attendance was tracked through user_meta.
	 * This function imports this legacy attendance information into the attendees table.
	 *
	 * Instead of looping through all users, we consider only users who have contributed to an event.
	 *
	 * @deprecated TODO: Remove this function once this has been run in production.
	 * @throws Exception
	 */
	private static function import_legacy_attendees(): void {
		$query = new WP_Query(
			array(
				'post_type' => Translation_Events::CPT,
			)
		);

		$events              = $query->get_posts();
		$stats_calculator    = new Stats_Calculator();
		$attendee_repository = Translation_Events::get_attendee_repository();
		foreach ( $events as $event ) {
			foreach ( $stats_calculator->get_contributors( $event->ID ) as $user ) {
				$attendee = new Attendee( $event->ID, $user->ID );
				$attendee_repository->insert_attendee( $attendee );
			}
		}
	}
}
