<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTION_TYPE_CREATED = 'created';

	function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$this->persist( $translation, self::ACTION_TYPE_CREATED );
			},
		);
	}

	private function persist( GP_Translation $translation, string $action_type ): void {
		// TODO.
	}
}
