<?php namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Attendee\Attendee;



register_block_type(
	'wporg-translate-events-2024/attendee-list',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes, $content, $block ) {
			if ( ! isset( $attributes['id'] ) ) {
				return '';
			}
			$event_id = $attributes['id'];
			$user_id = get_current_user_id();
			$attendees             = Translation_Events::get_attendee_repository()->get_attendees( $event_id );
			$current_user_attendee = $attendees[ $user_id ] ?? null;
			$user_is_attending     = $current_user_attendee instanceof Attendee;
			$attendees_not_contributing = array_filter(
				$attendees,
				function ( Attendee $attendee ) {
					return ! $attendee->is_contributor();
				}
			);

			ob_start();
			?>
			<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter"} -->
			<h4 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700"><?php echo esc_html( sprintf( __( 'Attendees (%d)', 'wporg-translate-events-2024' ), number_format_i18n( count( $attendees ) ) ) ); ?></h4>
			<!-- /wp:heading -->
			<!-- wp:group {"align":"full"} -->
			<div class="wp-block-group alignfull">
				<!-- wp:columns {"columns":3} -->
				<div class="wp-block-columns has-3-columns">
			<?php
			$columns = 3;
			$counter = 0;

			foreach ( $attendees_not_contributing as $attendee ) :
				if ( $columns === $counter ) :
					?>
					</div><!-- /wp:columns -->
					<!-- wp:columns {"columns":3} --><div class="wp-block-columns has-3-columns">
					<?php
				endif;
				?>
				<!-- wp:wporg-translate-events-2024/attendee-avatar-name <?php echo wp_json_encode( array( 'user_id' => $attendee->user_id(), 'is_new_contributor' => $attendee->is_new_contributor() ) ); ?> /-->
				<!-- wp:column -->
				<div class="wp-block-column">
				</div>
				<!-- /wp:column -->
				<?php
				$counter++;
			endforeach;
			?>
			</div><!-- /wp:columns -->
			</div><!-- /wp:group -->
				<?php
				return ob_get_clean();
		},
	)
);
