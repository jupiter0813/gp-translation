<?php

namespace Wporg\TranslationEvents;

class Templates {
	private string $base_path;

	public function __construct() {
		$this->base_path = __DIR__ . '/../templates/';
	}

	public function render( string $template, array $data = array() ) {
		gp_tmpl_load( $template, $data, $this->base_path );
	}
}
