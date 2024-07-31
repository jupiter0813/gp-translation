<?php namespace Wporg\TranslationEvents\Theme_2024;

register_block_type(
	'wporg-translate-events-2024/components-event-title',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup, $translation_event_current_user_attendee;
			$current_user_attendee = $translation_event_current_user_attendee[ $attributes['id'] ] ?? null;
			if ( $current_user_attendee ) {
				$event_flag = 'Attending';
				if ( $current_user_attendee->is_host() ) {
					$event_flag = 'Host';
				}
			}
			ob_start();
			?>
	
			<h3 class="wporg-marker-list-item__title">
					<a href="<?php echo esc_url( \Wporg\TranslationEvents\Urls::event_details( $attributes['id'] ) ); ?>">
						<?php echo esc_html( $translation_events_lookup[ $attributes['id'] ]->title() ); ?>
					</a>
					<?php
					if ( isset( $event_flag ) ) :
						?>
						<!-- wp:wporg-translate-events-2024/components-event-my-event-flag <?php echo wp_json_encode( array( 'my_event_flag' => $event_flag ) ); ?> /-->
						<?php
						endif;
					?>
				</h3>
			<?php
			return ob_get_clean();
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/components-event-start',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<time class="wporg-marker-list-item__date-time">' . esc_html( $translation_events_lookup[ $attributes['id'] ]->start()->format( 'F j, Y' ) ) . '</time>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/components-event-attendance-mode',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<div class="wporg-marker-list-item__location">
' . esc_html( $translation_events_lookup[ $attributes['id'] ]->attendance_mode() ) . '</div>';
		},
	)
);

register_block_type(
	'wporg-translate-events-2024/components-event-my-event-flag',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			global $translation_events_lookup;
			return '<span class="my-event-flag">' . esc_html( $attributes['my_event_flag'] ) . '</span>';
		},
	)
);
