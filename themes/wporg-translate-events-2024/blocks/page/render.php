<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $page_content */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?><?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/header /-->' ); ?>
	<?php echo $page_content; ?>
<?php echo do_blocks( '<!-- wp:wporg-translate-events-2024/footer /-->' ); ?>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
