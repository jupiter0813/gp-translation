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
				$happened_at = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $translation->date_added, new DateTimeZone( 'UTC' ) );
				$this->handle_action( $translation, $translation->user_id, self::ACTION_CREATE, $happened_at );
			},
		);

		add_action(
			'gp_translation_saved',
			function ( $translation, $translation_before ) {
				$user_id     = $translation->user_id_last_modified;
				$status      = $translation->status;
				$happened_at = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $translation->date_modified, new DateTimeZone( 'UTC' ) );

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

	private function handle_action( GP_Translation $translation, int $user_id, string $action, DateTimeImmutable $happened_at ): void {
		try {
			// Get events that are active when the action happened, for which the user is registered for.
			$active_events = $this->get_active_events( $happened_at );
			$events        = $this->select_events_user_is_registered_for( $active_events, $user_id );

			/** @var GP_Translation_Set $translation_set */
			$translation_set = ( new GP_Translation_Set )->find_one( [ 'id' => $translation->translation_set_id ] );
			global $wpdb;
			$table_name = self::ACTIONS_TABLE_NAME;

			foreach ( $events as $event ) {
				// A given user can only do one action on a specific translation.
				// So we insert ignore, which will keep only the first action.
				$wpdb->query(
					$wpdb->prepare(
						"insert ignore into $table_name (event_id, user_id, translation_id, action, locale) values (%d, %d, %d, %s, %s)",
						[
							// start primary key
							'event_id'       => $event->id(),
							'user_id'        => $user_id,
							'translation_id' => $translation->id,
							// end primary key
							'action'         => $action,
							'locale'         => $translation_set->locale,
						],
					),
				);
			}
		} catch ( Exception $exception ) {
			error_log( $exception );
		}
	}

	/**
	 * @return WPORG_GP_Translation_Events_Event[]
	 * @throws Exception
	 */
	private function get_active_events( DateTimeImmutable $at ): array {
		$events = $this->active_events_cache->get();
		if ( null === $events ) {
			$cache_duration = WPORG_GP_Translation_Events_Active_Events_Cache::CACHE_DURATION;
			$boundary_start = $at;
			$boundary_end   = $at->modify( "+$cache_duration seconds" );

			// Get events for which start is before $boundary_end AND end is after $boundary_start.
			$event_ids = get_posts(
				[
					'post_type'      => 'event',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'fields'         => 'ids',
					'meta_query'     => [
						[
							'key'     => '_event_start',
							'value'   => $boundary_end->format( 'Y-m-d H:i:s' ),
							'compare' => '<',
							'type'    => 'DATETIME',
						],
						[
							'key'     => '_event_end',
							'value'   => $boundary_start->format( 'Y-m-d H:i:s' ),
							'compare' => '>',
							'type'    => 'DATETIME',
						],
					],
				],
			);

			$events = [];
			foreach ( $event_ids as $event_id ) {
				$meta = get_post_meta( $event_id );
				if ( ! isset( $meta['_event_start'][0] ) || ! isset( $meta['_event_end'][0] ) || ! isset( $meta['_event_timezone'][0] ) ) {
					throw new Exception( 'Invalid event meta' );
				}
				$events[] = new WPORG_GP_Translation_Events_Event(
					$event_id,
					DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $meta['_event_start'][0], new DateTimeZone( 'UTC' ) ),
					DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $meta['_event_end'][0], new DateTimeZone( 'UTC' ) ),
					new DateTimeZone( $meta['_event_timezone'] ),
				);
			}

			$this->active_events_cache->cache( $events );
		}

		// Filter out events that aren't actually active at $at.
		return array_filter(
			$events,
			function ( $event ) use ( $at ) {
				return $at >= $event->start() && $at <= $event->end();
			}
		);
	}

	/**
	 * @param WPORG_GP_Translation_Events_Event[] $events
	 *
	 * @return WPORG_GP_Translation_Events_Event[]
	 */
	private function select_events_user_is_registered_for( array $events, int $user_id ): array {
		return array_filter(
			$events,
			function ( WPORG_GP_Translation_Events_Event $event ) {
				// TODO.
				return true;
			}
		);
	}
}
