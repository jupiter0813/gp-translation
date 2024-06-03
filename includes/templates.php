<?php

namespace Wporg\TranslationEvents;

class Templates {
	public static function render( string $template, array $data = array() ) {
		gp_tmpl_load( $template, $data, __DIR__ . '/../templates/' );
	}

	public static function header( array $data ) {
		self::part( 'header', $data );
	}

	public static function footer( array $data = array() ) {
		self::part( 'footer', $data );
	}

	public static function part( string $template, array $data ) {
		self::render( "parts/$template", $data );
	}
}
