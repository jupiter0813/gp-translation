<?php

namespace Wporg\TranslationEvents\Theme_2024;

class Templates {
	public static function block( string $name, array $data = array() ): string {
		$json = empty( $data ) ? '{}' : wp_json_encode( $data );
		return do_blocks( "<!-- wp:$name $json /-->" );
	}
}
