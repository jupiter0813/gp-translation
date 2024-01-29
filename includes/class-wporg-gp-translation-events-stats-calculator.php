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
	function for_event( WP_Post $event ): WPORG_GP_Translation_Events_Event_Stats {
		return new WPORG_GP_Translation_Events_Event_Stats( 42 );
	}
}
