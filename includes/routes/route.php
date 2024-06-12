<?php

namespace Wporg\TranslationEvents\Routes;

use GP_Route;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Translation_Events;

abstract class Route extends GP_Route {
	public function tmpl( $template, $args = array(), $honor_api = true ) {
		$this->set_notices_and_errors();
		$this->header( 'Content-Type: text/html; charset=utf-8' );

		Translation_Events::get_assets()->register();
		Templates::render( $template, $args );
	}
}
