<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-flag',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			ob_start();
			?>
			<span class="my-event-flag"><?php echo esc_html( $attributes['flag'] ); ?></span>
			<?php
			return ob_get_clean();
		},
	)
);
