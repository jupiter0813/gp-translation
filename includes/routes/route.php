<?php

namespace Wporg\TranslationEvents\Routes;

use GP_Route;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Theme_Loader;
use Wporg\TranslationEvents\Translation_Events;

abstract class Route extends GP_Route {
	private Theme_Loader $theme_loader;

	public function __construct() {
		parent::__construct();
		$this->theme_loader = new Theme_Loader( 'wporg-translate-events-2024' );
	}

	public function tmpl( $template, $args = array(), $honor_api = true ) {
		$this->set_notices_and_errors();
		$this->header( 'Content-Type: text/html; charset=utf-8' );

		Translation_Events::get_assets()->enqueue();
		Templates::render( $template, $args );
	}

	protected function use_theme( bool $also_in_production = false ): void {
		if ( $also_in_production ) {
			$use_theme = true;
		} else {
			// Only enable if new design has been explicitly enabled.
			$use_theme = defined( 'TRANSLATION_EVENTS_NEW_DESIGN' ) && TRANSLATION_EVENTS_NEW_DESIGN;
		}

		if ( ! $use_theme ) {
			return;
		}

		$this->theme_loader->load();
		Templates::set_template_directory( $this->theme_loader->get_theme_root() );
		Translation_Events::get_assets()->use_new_design();
	}
}
