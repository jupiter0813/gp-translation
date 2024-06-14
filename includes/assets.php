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
		$this->dequeue_unwanted_assets();
	}

	private function dequeue_unwanted_assets(): void {
		// Dequeue styles and scripts from glotpress and from the pub/wporg theme.
		// The WordPress.org theme enqueues styles in wp_enqueue_scripts so we need to dequeue in both styles and scripts.
		foreach ( array( 'wp_enqueue_styles', 'wp_enqueue_scripts' ) as $action ) {
			add_action(
				$action,
				function (): void {
					wp_styles()->remove(
						array(
							'wporg-style',
						)
					);
					wp_scripts()->remove(
						array(
							'gp-common',
							'wporg-plugins-skip-link-focus-fix',
						)
					);
				},
				9999 // Run as late as possible to make sure the styles/scripts are not enqueued after we dequeue them.
			);
		}
	}

	private function register_styles(): void {
		add_action(
			'wp_head',
			function (): void {
				wp_register_style(
					'translation-events-new-design-css',
					plugins_url( 'assets/css/new-design.css', $this->base_dir ),
					array(),
					filemtime( $this->base_dir . '/css/new-design.css' )
				);
				wp_enqueue_style( 'translation-events-new-design-css' );
			}
		);
	}

	private function register_legacy_styles(): void {
		wp_register_style(
			'translation-events-css',
			plugins_url( 'assets/css/translation-events.css', $this->base_dir ),
			array( 'dashicons' ),
			filemtime( $this->base_dir . '/css/translation-events.css' )
		);
		wp_enqueue_style( 'translation-events-css' );
	}

	private function register_scripts(): void {
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
