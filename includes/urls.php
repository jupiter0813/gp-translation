<?php

namespace Wporg\TranslationEvents;

class Urls {
	public static function event_details( int $event_id ): string {
		return gp_url( wp_make_link_relative( get_the_permalink( $event_id ) ) );
	}

	public static function event_details_absolute( int $event_id ): string {
		list( $permalink, $post_name ) = get_sample_permalink( $event_id );
		$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
		return str_replace( '%postname%', $post_name, $permalink );
	}
}
