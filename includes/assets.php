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

	public function register(): void {
		$this->register_scripts();

		if ( ! $this->use_new_design ) {
			$this->register_legacy_styles();
			return;
		}

		$this->register_styles();
	}

	private function register_styles(): void {
		// Remove GlotPress styles and scripts.
		wp_styles()->remove( 'gp-base' );
		wp_scripts()->remove( array( 'gp-common', 'gp-editor', 'gp-glossary', 'gp-translations-page', 'gp-mass-create-sets-page' ) );

		wp_register_style(
			'translation-events-new-design-css',
			plugins_url( 'assets/css/new-design.css', $this->base_dir ),
			array(),
			filemtime( $this->base_dir . '/css/new-design.css' )
		);
		gp_enqueue_styles( 'translation-events-new-design-css' );
	}

	private function register_legacy_styles(): void {
		wp_register_style(
			'translation-events-css',
			plugins_url( 'assets/css/translation-events.css', $this->base_dir ),
			array( 'dashicons' ),
			filemtime( $this->base_dir . '/css/translation-events.css' )
		);
		gp_enqueue_styles( 'translation-events-css' );
	}

	private function register_scripts(): void {
		wp_register_script(
			'translation-events-js',
			plugins_url( 'assets/js/translation-events.js', $this->base_dir ),
			array( 'jquery', 'gp-common' ),
			filemtime( $this->base_dir . '/js/translation-events.js' ),
			false
		);
		gp_enqueue_script( 'translation-events-js' );
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
