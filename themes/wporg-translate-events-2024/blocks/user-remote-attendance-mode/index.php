<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/user-remote-attendance-mode',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function () {
			return '<span class="dashicons dashicons-video-alt2"></span>';
		},
	)
);
