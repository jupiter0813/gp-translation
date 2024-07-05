<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $attributes */

$page_name       = $attributes['name'];
$page_attributes = $attributes['data'];
$page_block      = "wporg-translate-events-2024/events-pages-$page_name";

$page_content = do_blocks( "<!-- wp:$page_block " . wp_json_encode( $page_attributes ) . ' /-->' );

// The header and footer blocks must be rendered last, because other blocks may register styles or scripts,
// or modify the page title, or add breadcrumbs.
$header_content = do_blocks( '<!-- wp:wporg-translate-events-2024/header /-->' );
$footer_content = do_blocks( '<!-- wp:wporg-translate-events-2024/footer /-->' );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $header_content . $page_content . $footer_content;
