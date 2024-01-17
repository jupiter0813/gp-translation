<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTIONS_TABLE_NAME = 'wp_wporg_gp_translation_events_actions';

	public function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$this->handle_action( $translation );
			},
		);
	}

	private function handle_action( GP_Translation $translation ): void {
		$now = new DateTime( 'now', new DateTimeZone( 'UTC' ) );

		// Get events that are active now, for which the user is registered for.
		$active_events = $this->get_active_events( $now );
		$events        = $this->select_events_user_is_registered_for( $active_events, $translation->user_id );

		foreach ( $events as $event ) {
			$this->persist( $event, $translation, $now );
		}
	}

	private function persist(
		WP_Post $event,
		GP_Translation $translation,
		DateTime $created_at
	): void {
		/** @var GP_Translation_Set $translation_set */
		$translation_set = ( new GP_Translation_Set )->find_one( [ 'id' => $translation->translation_set_id ] );

		global $wpdb;
		$wpdb->insert(
			self::ACTIONS_TABLE_NAME,
			[
				'event_id'       => $event->ID,
				'user_id'        => $translation->user_id,
				'translation_id' => $translation->id,
				'created_at'     => $created_at->format( 'Y-m-d H:i:s' ),
				'locale'         => $translation_set->locale,
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
