<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/remote-attendance-icon',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( $attributes ) {
			$inline_css = isset( $attributes['inline_css'] ) ? esc_attr( $attributes['inline_css'] ) : '';

			return sprintf( '<span class="dashicons dashicons-video-alt2" style="%s"></span>', $inline_css );
		},
	)
);
