<?php

namespace Wporg\TranslationEvents;

class Styles {
	private bool $use_new_design;

	public function __construct( bool $use_new_design = false ) {
		$this->use_new_design = $use_new_design;
	}

	public function init(): void {
		if ( ! $this->use_new_design ) {
			$this->legacy_init();
			return;
		}

		// Remove GlotPress styles and scripts.
		wp_styles()->remove( 'gp-base' );
		wp_scripts()->remove( array( 'gp-common', 'gp-editor', 'gp-glossary', 'gp-translations-page', 'gp-mass-create-sets-page' ) );

		wp_register_style(
			'translation-events-new-design-css',
			plugins_url( 'assets/css/new-design.css', __DIR__ ),
			array( 'dashicons' ),
			filemtime( __DIR__ . '/../assets/css/new-design.css' )
		);
		gp_enqueue_styles( 'translation-events-new-design-css' );
	}

	private function legacy_init(): void {
		// TODO.
	}
}
