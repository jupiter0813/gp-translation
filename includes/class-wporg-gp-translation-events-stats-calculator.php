<?php

class WPORG_GP_Translation_Events_Stat {
	private int $value = 0;

	public function increment(): void {
		$this->value++;
	}

	public function value(): int {
		return $this->value;
	}
}

class WPORG_GP_Translation_Events_Event_Stats {
	/**
	 * Number of translations created during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $created;

	/**
	 * Number of translations approved during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $approved;

	/**
	 * Number of translations rejected during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $rejected;

	/**
	 * Number of translations marked fuzzy during the event.
	 */
	public WPORG_GP_Translation_Events_Stat $marked_fuzzy;

	public function __construct() {
		$this->created      = new WPORG_GP_Translation_Events_Stat;
		$this->approved     = new WPORG_GP_Translation_Events_Stat;
		$this->rejected     = new WPORG_GP_Translation_Events_Stat;
		$this->marked_fuzzy = new WPORG_GP_Translation_Events_Stat;
	}
}

class WPORG_GP_Translation_Events_Stats_Calculator {
	private string $ACTIONS_TABLE_NAME = WPORG_GP_Translation_Events_Translation_Listener::ACTIONS_TABLE_NAME;

	/**
	 * @throws Exception
	 */
	function for_event( WP_Post $event ): WPORG_GP_Translation_Events_Event_Stats {
		$start = new DateTime( get_post_meta( $event->ID, '_event_start_date', true ), new DateTimeZone( 'UTC' ) );
		$end   = new DateTime( get_post_meta( $event->ID, '_event_end_date', true ), new DateTimeZone( 'UTC' ) );

		global $wpdb;

		$query = $wpdb->prepare(
			"
				select action
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
		$results = $wpdb->get_results( $query );
		foreach ( $results as $result ) {
			/** @var WPORG_GP_Translation_Events_Stat $stat */
			$stat = null;
			switch ( $result->action ) {
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_CREATE:
					$stat = $stats->created;
					break;
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_APPROVE:
					$stat = $stats->approved;
					break;
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_REJECT:
					$stat = $stats->rejected;
					break;
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_MARK_FUZZY:
					$stat = $stats->marked_fuzzy;
					break;
				default:
					// Unknown action. Should not happen.
					break;
			}

			if ( $stat ) {
				$stat->increment();
			}
		}

		return $stats;
	}
}
