<?php

class WPORG_GP_Translation_Events_Stat {
	private int $value;

	public function __construct( int $value = 0 ) {
		$this->value = $value;
	}

	public function increment(): void {
		$this->value++;
	}

	public function value(): int {
		return $this->value;
	}
}

class WPORG_GP_Translation_Events_Event_Stats {
	/**
	 * Ids of users who participated in the event.
	 * @var int[]
	 */
	private array $users;

	/**
	 * Number of translations created during the event.
	 */
	private WPORG_GP_Translation_Events_Stat $created;

	/**
	 * Number of translations reviewed (approved, rejected, etc.) during the event.
	 */
	private WPORG_GP_Translation_Events_Stat $reviewed;

	public function __construct() {
		$this->created  = new WPORG_GP_Translation_Events_Stat;
		$this->reviewed = new WPORG_GP_Translation_Events_Stat;
	}

	public function add_user( int $user_id ) {
		$this->users[$user_id] = true;
	}

	public function users(): WPORG_GP_Translation_Events_Stat {
		return new WPORG_GP_Translation_Events_Stat(count($this->users));
	}

	public function created(): WPORG_GP_Translation_Events_Stat {
		return $this->created;
	}

	public function reviewed(): WPORG_GP_Translation_Events_Stat {
		return $this->reviewed;
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

		$stats_total = new WPORG_GP_Translation_Events_Event_Stats;

		$results = $wpdb->get_results( $query );
		foreach ( $results as $result ) {
			/** @var WPORG_GP_Translation_Events_Stat $stat */
			$stat = null;
			switch ( $result->action ) {
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_CREATE:
					$stat = $stats_total->created();
					break;
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_APPROVE:
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_REJECT:
				case WPORG_GP_Translation_Events_Translation_Listener::ACTION_MARK_FUZZY:
					$stat = $stats_total->reviewed();
					break;
				default:
					// Unknown action. Should not happen.
					break;
			}

			if ( $stat ) {
				$stat->increment();
				$stats_total->add_user($result->user_id);
			}
		}

		return $stats_total;
	}
}
