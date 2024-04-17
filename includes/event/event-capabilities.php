<?php

namespace Wporg\TranslationEvents\Event;

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
		// TODO.
		return true;
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
