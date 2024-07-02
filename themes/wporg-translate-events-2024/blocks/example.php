<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events/example',
	array(
		'render_callback' => function ( $attributes, $content ) {
			// TODO.
			return '<span>hello</span>';
		},
	)
);
