<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/event-title',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			ob_start();
			?>
			<h3 class="wporg-marker-list-item__title">
					<a href="<?php echo esc_url( $attributes['url'] ); ?>">
						<?php echo esc_html( $attributes['title'] ); ?>
					</a>
					<?php
					if ( isset( $attributes['flag'] ) ) {

						$json = wp_json_encode( array( 'flag' => $attributes['flag'] ) );
						echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							<<<BLOCKS
							<!-- wp:wporg-translate-events-2024/event-flag $json /-->
							BLOCKS
						);
					}
					?>
				</h3>
			<?php
			return ob_get_clean();
		},
	)
);
