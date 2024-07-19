<?php namespace Wporg\TranslationEvents\Blocks\EventStartDate;

use WP_Block_Type;
use Wporg\TranslationEvents\Translation_Events;

add_filter(
	'get_block_type_variations',
	function ( array $variations, WP_Block_Type $block_type ) {
		$variation_name = 'wporg-translate/start-date-variation';
		if ( 'wporg-translate-events/start-date' === $block_type->name ) {
			$variations[] = array(
				'name'            => $variation_name,
				'title'           => __( 'Start Date Variation', 'gp-translation-events' ),
				'description'     => __( 'Variation of the start date', 'gp-translation-events' ),
				'icon'            => 'calendar-alt',
				'isActive'        => array( 'namespace' ),
				'allowedControls' => array(),
			);
		}

		return $variations;
	},
	10,
	2
);
