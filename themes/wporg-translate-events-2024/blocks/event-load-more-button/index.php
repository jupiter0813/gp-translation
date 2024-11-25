<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/event-load-more-button',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( $attributes ) {
			$event_filter = $attributes['filter'];
			ob_start();
			?>
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline">
				<button class="wp-block-button__link wp-element-button load-more-events-btn" data-event-type="<?php echo esc_attr( $event_filter ); ?>" ><?php esc_html_e( 'Load more', 'gp-translation-events' ); ?></button>
			</div>
			<!-- /wp:button -->
			<?php
			return ob_get_clean();
		},
	)
);
