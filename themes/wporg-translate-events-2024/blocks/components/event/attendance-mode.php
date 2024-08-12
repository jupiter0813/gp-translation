<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-attendance-mode',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			return '<div class="wporg-marker-list-item__location">
' . esc_html( $attributes['attendance_mode'] ) . '</div>';
		},
	)
);
