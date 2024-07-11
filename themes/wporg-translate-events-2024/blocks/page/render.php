<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $attributes */

$page_name       = $attributes['page_name'];
$page_attributes = $attributes['page_attributes'];

// First render the block of the given page, so that it modifies title, breadcrumbs, etc.
$page_content = do_blocks( "<!-- wp:wporg-translate-events-2024/pages-$page_name " . wp_json_encode( $page_attributes ) . ' /-->' );

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?><?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/header /-->' ); ?>
	<?php echo $page_content; ?>
<?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/footer /-->' ); ?>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
