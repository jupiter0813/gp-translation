<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTIONS_TABLE_NAME = 'wp_wporg_gp_translation_events_actions';
	const ACTION_CREATE = 'create';
	const ACTION_APPROVE = 'approve';

	public function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$happened_at = DateTime::createFromFormat( 'Y-m-d H:i:s', $translation->date_created, DateTimeZone::UTC );
				$this->handle_action( $translation, $translation->user_id, self::ACTION_CREATE, $happened_at );
			},
		);

		add_action(
			'gp_translation_saved',
			function ( $translation, $translation_before ) {
				$user_id       = $translation->user_id_last_modified;
				$status        = $translation->status;
				$status_before = $translation_before->status;
				$happened_at   = DateTime::createFromFormat( 'Y-m-d H:i:s', $translation->date_modified, DateTimeZone::UTC );

				if ( 'current' === $status && 'current' !== $status_before ) {
					$this->handle_action( $translation, $user_id, self::ACTION_APPROVE, $happened_at );
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

		foreach ( $events as $event ) {
			$this->persist( $event, $translation, $user_id, $happened_at, $action );
		}
	}

	private function persist(
		WP_Post $event,
		GP_Translation $translation,
		int $user_id,
		DateTime $happened_at,
		string $action
	): void {
		/** @var GP_Translation_Set $translation_set */
		$translation_set = ( new GP_Translation_Set )->find_one( [ 'id' => $translation->translation_set_id ] );

		global $wpdb;

		// A given user can only do one action of a given type on a specific translation.
		// So we replace instead of insert, which will enforce the primary key.
		$wpdb->replace(
			self::ACTIONS_TABLE_NAME,
			[
				// start primary key
				'event_id'       => $event->ID,
				'user_id'        => $user_id,
				'translation_id' => $translation->id,
				'action'         => $action,
				// end primary key
				'locale'         => $translation_set->locale,
				'happened_at'    => $happened_at->format( 'Y-m-d H:i:s' ),
			]
		);
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
						'key'     => '_event_start_date',
						'value'   => $at->format( 'Y-m-d' ),
						'compare' => '<=',
						'type'    => 'DATETIME',
					],
					[
						'key'     => '_event_end_date',
						'value'   => $at->format( 'Y-m-d' ),
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
