<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/event-start',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			return '<time class="wporg-marker-list-item__date-time">' . esc_html( $attributes['date'] ) . '</time>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/event-end',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			return '<p>' . esc_html( $attributes['date'] ) . '</p>';
		},
	)
);
