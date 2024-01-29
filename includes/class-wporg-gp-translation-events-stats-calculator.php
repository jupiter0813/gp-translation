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
	/**
	 * @throws Exception
	 */
	function for_event( WP_Post $event ): WPORG_GP_Translation_Events_Event_Stats {
		$start = new DateTime( get_post_meta( $event->ID, '_event_start_date', true ), new DateTimeZone( 'UTC' ) );
		$end   = new DateTime( get_post_meta( $event->ID, '_event_end_date', true ), new DateTimeZone( 'UTC' ) );

		return new WPORG_GP_Translation_Events_Event_Stats( 42 );
	}
}
