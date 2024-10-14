<?php
namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/remote-attendance-icon',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function () {
			return '<span class="user-remote-icon dashicons dashicons-video-alt2"></span>';
		},
	)
);
