<?php
namespace Wporg\TranslationEvents\Templates\NewDesign\Blocks;

register_block_type(
	'wporg-translate-events/example',
	array(
		'render_callback' => function ( $attributes, $content ) {
			// TODO.
			return '<span>hello</span>';
		},
	)
);
