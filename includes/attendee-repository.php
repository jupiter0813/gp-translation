<?php

namespace Wporg\TranslationEvents;

class Attendee_Repository {
	public function add_attendee( int $event_id, int $user_id ): void {
		// TODO.
	}

	public function remove_attendee( int $event_id, int $user_id ): void {
		// TODO.
	}

	public function is_attending( int $event_id, int $user_id ): bool {
		// TODO.
		return false;
	}

	/**
	 * @return int[] User ids.
	 */
	public function get_attendees( int $event_id ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// TODO.
		return array();
	}
}
