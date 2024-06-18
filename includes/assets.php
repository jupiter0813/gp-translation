<?php

namespace Wporg\TranslationEvents;

class Assets {
	private string $base_dir;
	private bool $use_new_design;

	public function __construct() {
		$this->base_dir       = realpath( __DIR__ . '/../assets' );
		$this->use_new_design = false;
	}

	public function use_new_design(): void {
		$this->use_new_design = true;
	}

	public function enqueue(): void {
		if ( ! $this->use_new_design ) {
			$this->enqueue_legacy_styles();
		}
	}

	private function enqueue_legacy_styles(): void {
		wp_register_style(
			'translation-events-css',
			plugins_url( 'assets/css/translation-events.css', $this->base_dir ),
			array( 'dashicons' ),
			filemtime( $this->base_dir . '/css/translation-events.css' )
		);
		wp_enqueue_style( 'translation-events-css' );
	}
}
