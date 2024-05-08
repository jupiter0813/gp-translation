<?php

namespace Wporg\TranslationEvents;

class Templates {
	public static function render( string $template, array $data = array() ) {
		gp_tmpl_load( $template, $data, __DIR__ . '/../templates/' );
	}

	public static function header( array $data ) {
		self::render( 'header', $data );
	}

	public static function footer( array $data = array() ) {
		self::render( 'footer', $data );
	}

	public static function partial( string $template, array $data ) {
		self::render( "partials/$template", $data );
	}
}
