<?php

namespace Wporg\TranslationEvents;

class Theme_Loader {
	private string $theme;

	public function __construct( string $theme ) {
		$this->theme = $theme;
	}

	public function load(): void {
		if ( str_ends_with( get_stylesheet_directory(), $this->theme ) ) {
			// Our theme is already the active theme, there's nothing to do here.
			return;
		}

		add_filter(
			'template',
			function (): string {
				// TODO: Calculate automatically.
				return 'wporg-parent-2021';
			}
		);
		add_filter(
			'stylesheet',
			function (): string {
				return $this->theme;
			}
		);

		global $wp_stylesheet_path, $wp_template_path;
		$wp_stylesheet_path = get_stylesheet_directory();
		$wp_template_path   = get_template_directory();

		foreach ( wp_get_active_and_valid_themes() as $theme ) {
			if ( file_exists( $theme . '/functions.php' ) ) {
				include $theme . '/functions.php';
			}
		}
	}
}
