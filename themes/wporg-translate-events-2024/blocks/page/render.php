<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $attributes */

$page_name       = $attributes['page_name'];
$page_attributes = $attributes['page_attributes'];
$page_block      = "wporg-translate-events-2024/pages-$page_name";

$page_content = do_blocks( "<!-- wp:$page_block " . wp_json_encode( $page_attributes ) . ' /-->' );

// The header and footer blocks must be rendered last, because other blocks may register styles or scripts,
// or modify the page title, or add breadcrumbs.
$header_content = do_blocks( '<!-- wp:wporg-translate-events-2024/header /-->' );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $header_content;
?>
			<?php echo $page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div> <?php // Close wp-site-blocks div, opened in header block. ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo do_blocks( '<!-- wp:wporg/global-footer /-->' ); ?>
		<?php wp_footer(); ?>
	</body>
</html>
