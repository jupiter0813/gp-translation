<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $attributes */

$page_name       = $attributes['page_name'];
$page_attributes = $attributes['page_attributes'];
$page_block      = "wporg-translate-events-2024/pages-$page_name";

// First render the block of the given page, so that it modifies title, breadcrumbs, etc.
$page_content = do_blocks( "<!-- wp:$page_block " . wp_json_encode( $page_attributes ) . ' /-->' );

?><?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/header /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo $page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/footer /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php
