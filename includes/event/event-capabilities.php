<?php

namespace Wporg\TranslationEvents\Event;

use GP;
use WP_User;

class Event_Capabilities {
	private const CREATE = 'create_translation_event';

	private const CAPS = array(
		self::CREATE,
	);

	private function has_cap( string $cap, array $args, WP_User $user ): bool {
		switch ( $cap ) {
			case self::CREATE:
				return $this->has_create( $user );
		}

		return false;
	}

	private function has_create( WP_User $user ): bool {
		return $this->has_gp_crud( $user );
	}

	private function has_gp_crud( WP_User $user ): bool {
		return apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->user_can( $user, 'admin' ) );
	}

	public function register_hooks(): void {
		add_action(
			'user_has_cap',
			function ( $allcaps, $caps, $args, $user ) {
				foreach ( $caps as $cap ) {
					if ( ! in_array( $cap, self::CAPS, true ) ) {
						continue;
					}
					if ( $this->has_cap( $cap, $args, $user ) ) {
						$allcaps[ $cap ] = true;
					}
				}
				return $allcaps;
			},
			10,
			4,
		);
	}
}
