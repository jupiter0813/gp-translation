<?php

class WPORG_GP_Translation_Events_Stats_Row {
	public int $created;
	public int $reviewed;
	public int $users;

	public function __construct( $created, $reviewed, $users ) {
		$this->created  = $created;
		$this->reviewed = $reviewed;
		$this->users    = $users;
	}
}

class WPORG_GP_Translation_Events_Event_Stats {
	/**
	 * Associative array of rows, with the locale as key.
	 *
	 * @var WPORG_GP_Translation_Events_Stats_Row[]
	 */
	private array $rows = [];

	private WPORG_GP_Translation_Events_Stats_Row $totals;

	/**
	 * @throws Exception
	 */
	public function add_row( string $locale, WPORG_GP_Translation_Events_Stats_Row $row ) {
		if ( ! $locale ) {
			throw new Exception( 'locale must not be empty' );
		}
		$this->rows[ $locale ] = $row;
	}

	public function set_totals( WPORG_GP_Translation_Events_Stats_Row $totals ) {
		$this->totals = $totals;
	}

	/**
	 * Get an associative array of rows, with the locale as key.
	 *
	 * @return WPORG_GP_Translation_Events_Stats_Row[]
	 */
	public function rows(): array {
		return $this->rows;
	}

	public function totals(): WPORG_GP_Translation_Events_Stats_Row {
		return $this->totals;
	}
}

class WPORG_GP_Translation_Events_Stats_Calculator {
	private string $ACTIONS_TABLE_NAME = WPORG_GP_Translation_Events_Translation_Listener::ACTIONS_TABLE_NAME;

	/**
	 * @throws Exception
	 */
	function for_event( WP_Post $event ): WPORG_GP_Translation_Events_Event_Stats {
		$stats = new WPORG_GP_Translation_Events_Event_Stats;
		global $wpdb;

		$query = $wpdb->prepare( "
				select locale,
					   sum(action = 'create') as created,
					   count(*) as total,
					   count(distinct user_id) as users
				from $this->ACTIONS_TABLE_NAME
				where event_id = %d
				group by locale with rollup
			",
			[
				$event->ID,
			]
		);

		$rows = $wpdb->get_results( $query );
		foreach ( $rows as $index => $row ) {
			$is_totals = $row->locale === null;
			if ( $is_totals && $index !== array_key_last( $rows ) ) {
				// If this is not the last row, something is wrong in the data in the database table
				// or there's a bug in the query above.
				throw new Exception(
					'Only the last row should have no locale but we found a non-last row with no locale.'
				);
			}

			$stats_row = new WPORG_GP_Translation_Events_Stats_Row(
				$row->created,
				$row->total - $row->created,
				$row->users,
			);

			if ( ! $is_totals ) {
				$stats->add_row( $row->locale, $stats_row );
			} else {
				$stats->set_totals( $stats_row );
			}
		}

		return $stats;
	}
}
