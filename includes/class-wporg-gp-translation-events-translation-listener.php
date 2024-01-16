<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTION_TYPE_CREATED = 'created';

	public function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$this->handle_action( $translation, self::ACTION_TYPE_CREATED );
			},
		);
	}

	private function handle_action( GP_Translation $translation, string $action_type ): void {
		// Get events that are active now, for which the user is registered for.
		$active_events = $this->get_active_events( new DateTime() );
		$events = $this->select_events_user_is_registered_for( $active_events, $translation->user_id );

		foreach ( $events as $event ) {
			$this->persist( $translation, $event );
		}
	}

	private function persist( GP_Translation $translation, WP_Post $event ): void {
		// TODO.
	}

	/**
	 * @return WP_Post[]
	 */
	private function get_active_events( DateTime $at ): array {
		// TODO.
		return [];
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
