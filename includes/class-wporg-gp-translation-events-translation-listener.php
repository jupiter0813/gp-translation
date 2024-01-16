<?php

class WPORG_GP_Translation_Events_Translation_Listener {
	const ACTION_TYPE_CREATED = 'created';

	public function start(): void {
		add_action(
			'gp_translation_created',
			function ( $translation ) {
				$this->handle_action( $translation, self::ACTION_TYPE_CREATED );
			},
		);
	}

	private function handle_action( GP_Translation $translation, string $action_type ): void {
		// TODO.
	}
}
