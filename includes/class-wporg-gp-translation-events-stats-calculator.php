<?php

class WPORG_GP_Translation_Events_Stat {
	private int $value = 0;

	public function increment(): void {
		$this->value++;
	}

	public function value(): int {
		return $this->value;
	}

	public function set( int $value ): void {
		$this->value = $value;
	}
}

class WPORG_GP_Translation_Events_Event_Stats {
	/**
	 * Number of users who performed an action during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $users;

	/**
	 * Number of translations created during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $created;

	/**
	 * Number of translations reviewed (approved, rejected, etc) during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $reviewed;

	public function __construct() {
		$this->users        = new WPORG_GP_Translation_Events_Stat;
		$this->created      = new WPORG_GP_Translation_Events_Stat;
		$this->reviewed     = new WPORG_GP_Translation_Events_Stat;
	}
}

class WPORG_GP_Translation_Events_Stats_Calculator {
	private string $ACTIONS_TABLE_NAME = WPORG_GP_Translation_Events_Translation_Listener::ACTIONS_TABLE_NAME;

	/**
	 * @throws Exception
	 */
	function for_event( WP_Post $event ): WPORG_GP_Translation_Events_Event_Stats {
		$start = new DateTime( get_post_meta( $event->ID, '_event_start', true ), new DateTimeZone( 'UTC' ) );
		$end   = new DateTime( get_post_meta( $event->ID, '_event_end', true ), new DateTimeZone( 'UTC' ) );

		global $wpdb;

		$query = $wpdb->prepare(
			"
				select action, user_id
				from $this->ACTIONS_TABLE_NAME
				where event_id = %d
				  and happened_at between cast('%s' as datetime) and cast('%s' as datetime)
			",
			[
				$event->ID,
				$start->format( 'Y-m-d H:i:s' ),
				$end->format( 'Y-m-d H:i:s' ),
			]
		);

		$stats = new WPORG_GP_Translation_Events_Event_Stats;
		$users = [];

		$results = $wpdb->get_results( $query );
		foreach ( $results as $result ) {
			/** @var WPORG_GP_Translation_Events_Stat $stat */
			$stat = null;
			switch ( $result->action ) {
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_CREATE:
					$stat = $stats->created;
					break;
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_APPROVE:
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_REJECT:
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_MARK_FUZZY:
					$stat = $stats->reviewed;
					break;
				default:
					// Unknown action. Should not happen.
					break;
			}

			if ( $stat ) {
				$stat->increment();
				$users[$result->user_id] = true;
			}
		}

		$stats->users->set(count($users));
		return $stats;
	}
}
