<?php

namespace Wporg\TranslationEvents\Routes;

use GP_Route;
use Wporg\TranslationEvents\Templates;

abstract class Route extends GP_Route {
	private Templates $templates;

	public function __construct() {
		parent::__construct();
		$this->templates     = new Templates();
		$this->template_path = __DIR__ . '/../../templates/';
	}

	protected function render( string $template, $data = array() ) {
		if ( $this->fake_request ) {
			$this->rendered_template = true;
			$this->loaded_template   = $template;
		}

		$this->set_notices_and_errors();
		$this->header( 'Content-Type: text/html; charset=utf-8' );

		if ( $this->fake_request ) {
			$this->template_output = gp_tmpl_get_output( $template, $data, $this->template_path );
			return;
		}

		$this->templates->render( $template, $data );
	}
}
