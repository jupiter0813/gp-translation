<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTIONS_TABLE_NAME = 'wp_wporg_gp_translation_events_actions';
	const ACTION_CREATE = 'create';
	const ACTION_APPROVE = 'approve';
	const ACTION_REJECT = 'reject';
	const ACTION_REQUEST_CHANGES = 'request_changes';

	private WPORG_GP_Translation_Events_Active_Events_Cache $active_events_cache;

	public function __construct( WPORG_GP_Translation_Events_Active_Events_Cache $active_events_cache ) {
		$this->active_events_cache = $active_events_cache;
	}

	public function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$happened_at = DateTime::createFromFormat( 'Y-m-d H:i:s', $translation->date_added, new DateTimeZone( 'UTC' ) );
				$this->handle_action( $translation, $translation->user_id, self::ACTION_CREATE, $happened_at );
			},
		);

		add_action(
			'gp_translation_saved',
			function ( $translation, $translation_before ) {
				$user_id     = $translation->user_id_last_modified;
				$status      = $translation->status;
				$happened_at = DateTime::createFromFormat( 'Y-m-d H:i:s', $translation->date_modified, new DateTimeZone( 'UTC' ) );

				if ( $translation_before->status === $status ) {
					// Translation hasn't changed status, so there's nothing for us to track.
					return;
				}

				$action = null;
				switch ( $status ) {
					case 'current':
						$action = self::ACTION_APPROVE;
						break;
					case 'rejected':
						$action = self::ACTION_REJECT;
						break;
					case 'changesrequested':
						$action = self::ACTION_REQUEST_CHANGES;
						break;
				}

				if ( $action ) {
					$this->handle_action( $translation, $user_id, $action, $happened_at );
				}
			},
			10,
			2,
		);
	}

	private function handle_action( GP_Translation $translation, int $user_id, string $action, DateTime $happened_at ): void {
		// Get events that are active when the action happened, for which the user is registered for.
		$active_events = $this->get_active_events( $happened_at );
		$events        = $this->select_events_user_is_registered_for( $active_events, $user_id );

		/** @var GP_Translation_Set $translation_set */
		$translation_set = ( new GP_Translation_Set )->find_one( [ 'id' => $translation->translation_set_id ] );
		global $wpdb;

		foreach ( $events as $event ) {
			// A given user can only do one action on a specific translation.
			// So we replace instead of insert, which will keep only the last action.
			$wpdb->replace(
				self::ACTIONS_TABLE_NAME,
				[
					// start primary key
					'event_id'       => $event->ID,
					'user_id'        => $user_id,
					'translation_id' => $translation->id,
					// end primary key
					'action'         => $action,
					'locale'         => $translation_set->locale,
					'happened_at'    => $happened_at->format( 'Y-m-d H:i:s' ),
				]
			);
		}
	}

	/**
	 * @return WP_Post[]
	 */
	private function get_active_events( DateTime $at ): array {
		return get_posts(
			[
				'post_type'   => 'event',
				'post_status' => 'publish',
				'meta_query'  => [
					[
						'key'     => '_event_start',
						'value'   => $at->format( 'Y-m-d H:i:s' ),
						'compare' => '<=',
						'type'    => 'DATETIME',
					],
					[
						'key'     => '_event_end',
						'value'   => $at->format( 'Y-m-d H:i:s' ),
						'compare' => '>=',
						'type'    => 'DATETIME',
					],
				],
			],
		);
	}

	/**
	 * @param WP_Post[] $events
	 *
	 * @return WP_Post[]
	 */
	private function select_events_user_is_registered_for( array $events, int $user_id ): array {
		return array_filter(
			$events,
			function ( $event ) {
				// TODO.
				return true;
			}
		);
	}
}
