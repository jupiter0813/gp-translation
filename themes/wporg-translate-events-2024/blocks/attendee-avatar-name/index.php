<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/attendee-avatar-name',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes, $content, $block ) {
			if ( ! isset( $attributes['user_id'] ) ) {
				return '';
			}
			$user_id = $attributes['user_id'];

			ob_start();
			?>
			<!-- wp:group -->
				<div class="wp-block-group">
				<a href="<?php echo esc_url( get_author_posts_url( $user_id ) ); ?>" class="attendee-avatar"><?php echo get_avatar( $user_id, 48 ); ?></a>
					<a href="<?php echo esc_url( get_author_posts_url( $user_id ) ); ?>" class="attendee-name"><?php echo esc_html( get_the_author_meta( 'display_name', $user_id ) ); ?></a>
			</div>
			<!-- /wp:group -->
			<?php
			return ob_get_clean();
		},
	)
);
