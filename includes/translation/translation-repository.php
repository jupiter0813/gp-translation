<?php

namespace Wporg\TranslationEvents\Translation;

use DateTimeImmutable;

class Translation_Repository {
	/**
	 * Count the number of translations made by given users before a specified datetime.
	 *
	 * @param int[]             $user_ids Ids of users for which to count translations.
	 * @param DateTimeImmutable $before   Only include translations before this datetime.
	 *
	 * @return array Associative array with user id as key and number of translations as value.
	 */
	public function count_translations_before( array $user_ids, DateTimeImmutable $before ): array {
		// TODO.
		return array();
	}
}
