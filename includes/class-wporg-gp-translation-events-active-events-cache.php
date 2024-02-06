<?php

class WPORG_GP_Translation_Events_Active_Events_Cache {
	public const CACHE_DURATION = 60 * 60 * 24; // 24 hours.
	private const KEY = 'translation-events-active-events';

	/**
	 * @param int[] $event_ids
	 *
	 * @throws Exception
	 */
	public function cache( array $event_ids ): void {
		if ( ! wp_cache_set( self::KEY, $event_ids, '', self::CACHE_DURATION ) ) {
			throw new Exception( 'Failed to cache active events' );
		}
	}

	/**
	 * Returns the cached event ids, or null if nothing is cached.
	 *
	 * @return int[]|null
	 * @throws Exception
	 */
	public function get(): ?array {
		$result = wp_cache_get( self::KEY, '', false, $found );
		if ( ! $found ) {
			return null;
		}

		if ( ! is_array( $result ) ) {
			throw new Exception( 'Cached event ids is not array, something is wrong' );
		}

		return $result;
	}

	/**
	 * @throws Exception
	 */
	public function invalidate(): void {
		if ( ! wp_cache_delete( self::KEY ) ) {
			throw new Exception( 'Failed to invalidate cached event ids' );
		}
	}
}
