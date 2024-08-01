<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;

register_block_type(
	'wporg-translate-events-2024/components-event-title',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Translation_Events::get_event_repository()->get_event( $attributes['id'] );
			$event_ids = $attributes['event_ids'] ?? array();
			$user_id = get_current_user_id();
			$current_user_attendee_per_event = Translation_Events::get_attendee_repository()->get_attendees_for_events_for_user( $event_ids, $user_id );
			$current_user_attendee = $current_user_attendee_per_event[ $event->id() ] ?? null;

			if ( $current_user_attendee ) {
				$event_flag = 'Attending';
				if ( $current_user_attendee->is_host() ) {
					$event_flag = 'Host';
				}
			}
			ob_start();
			?>
			<h3 class="wporg-marker-list-item__title">
					<a href="<?php echo esc_url( Urls::event_details( $attributes['id'] ) ); ?>">
						<?php echo esc_html( $event->title() ); ?>
					</a>
					<?php
					if ( isset( $event_flag ) ) :
						?>
						<span class="my-event-flag"><?php echo esc_html( $event_flag ); ?></span>
						<?php
						endif;
					?>
				</h3>
			<?php
			return ob_get_clean();
		},
	)
);
