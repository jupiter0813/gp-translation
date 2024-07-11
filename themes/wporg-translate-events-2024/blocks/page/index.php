<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/page',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$page_name       = $attributes['page_name'];
			$page_attributes = $attributes['page_attributes'];

			// First render the block of the given page, so that it modifies title, breadcrumbs, etc.
			$page_content = do_blocks( "<!-- wp:wporg-translate-events-2024/pages-$page_name " . wp_json_encode( $page_attributes ) . ' /-->' );

			ob_start();
			include_once __DIR__ . '/render.php';
			return do_blocks( ob_get_clean() );
		},
	)
);
