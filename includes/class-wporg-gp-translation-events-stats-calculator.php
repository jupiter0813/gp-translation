<?php

class WPORG_GP_Translation_Events_Event_Stats {
	/**
	 * Number of translations created during the event.
	 */
	public int $created;

	public function __construct( int $created ) {
		$this->created = $created;
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

		$created = 0;
		$results = $wpdb->get_results( $query );
		foreach ( $results as $result ) {
			switch ( $result->action ) {
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_CREATE:
					$created ++;
					break;
				default:
					// Unknown action. Should not happen.
					break;
			}
		}

		return new WPORG_GP_Translation_Events_Event_Stats( $created );
	}
}
