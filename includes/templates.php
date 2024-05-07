<?php

namespace Wporg\TranslationEvents;

class Templates {
	private string $base_path;

	public function __construct() {
		$this->base_path = __DIR__ . '/../templates/';
	}

	public static function get_instance(): Templates {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	public function render( string $template, array $data = array() ) {
		gp_tmpl_load( $template, $data, $this->base_path );
	}

	public static function header( array $data ) {
		self::get_instance()->render( 'header', $data );
	}
}
