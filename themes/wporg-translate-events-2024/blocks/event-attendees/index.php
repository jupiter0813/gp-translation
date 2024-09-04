<?php namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Attendee\Attendee;



register_block_type(
	'wporg-translate-events-2024/event-attendees',
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
			<?php
			return ob_get_clean();
		},
	)
);
