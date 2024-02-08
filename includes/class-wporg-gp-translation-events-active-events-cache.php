<?php


class WPORG_GP_Translation_Events_Active_Events_Cache {
	public const CACHE_DURATION = 60 * 60 * 24; // 24 hours.
	private const KEY = 'translation-events-active-events';

	/**
	 * @param WPORG_GP_Translation_Events_Event[] $events
	 *
	 * @throws Exception
	 */
	public function cache( array $events ): void {
		if ( ! wp_cache_set( self::KEY, $events, '', self::CACHE_DURATION ) ) {
			throw new Exception( 'Failed to cache active events' );
		}
	}

	/**
	 * Returns the cached events, or null if nothing is cached.
	 *
	 * @return WPORG_GP_Translation_Events_Event[]|null
	 * @throws Exception
	 */
	public function get(): ?array {
		$result = wp_cache_get( self::KEY, '', false, $found );
		if ( ! $found ) {
			return null;
		}

		if ( ! is_array( $result ) ) {
			throw new Exception( 'Cached events is not an array, something is wrong' );
		}

		return $result;
	}

	/**
	 * @throws Exception
	 */
	public static function invalidate(): void {
		if ( ! wp_cache_delete( self::KEY ) ) {
			throw new Exception( 'Failed to invalidate cached events' );
		}
	}
}
