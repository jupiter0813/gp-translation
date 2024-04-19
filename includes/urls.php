<?php

namespace Wporg\TranslationEvents;

class Urls {
	public static function event_details( int $event_id ): string {
		return gp_url( wp_make_link_relative( get_the_permalink( $event_id ) ) );
	}
}
