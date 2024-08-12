<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-title',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			ob_start();
			?>
			<h3 class="wporg-marker-list-item__title">
					<a href="<?php echo esc_url( $attributes['url'] ); ?>">
						<?php echo esc_html( $attributes['title'] ); ?>
					</a>
				</h3>
			<?php
			return ob_get_clean();
		},
	)
);
