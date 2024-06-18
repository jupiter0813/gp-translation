<?php

namespace Wporg\TranslationEvents;

class Assets {
	private string $base_dir;
	private bool $use_new_design;
	private Theme_Loader $theme_loader;

	public function __construct() {
		$this->base_dir       = realpath( __DIR__ . '/../assets' );
		$this->use_new_design = false;
		$this->theme_loader   = new Theme_Loader( 'wporg-translate-events-2024' );
	}

	public function use_new_design(): void {
		$this->use_new_design = true;
	}

	public function enqueue(): void {
		$this->enqueue_scripts();

		if ( ! $this->use_new_design ) {
			$this->enqueue_legacy_styles();
			return;
		}

		$this->theme_loader->load();
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

	private function enqueue_scripts(): void {
		wp_register_script(
			'translation-events-js',
			plugins_url( 'assets/js/translation-events.js', $this->base_dir ),
			array( 'jquery', 'gp-common' ),
			filemtime( $this->base_dir . '/js/translation-events.js' ),
			false
		);
		wp_enqueue_script( 'translation-events-js' );
		wp_localize_script(
			'translation-events-js',
			'$translation_event',
			array(
				'url'          => admin_url( 'admin-ajax.php' ),
				'_event_nonce' => wp_create_nonce( Translation_Events::CPT ),
			)
		);
	}
}
