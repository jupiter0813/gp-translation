<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;


register_block_type(
	'wporg-translate-events-2024/component-event-list',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event_ids = $attributes['event_ids'] ?? array();
			if ( empty( $event_ids ) ) {
				return;
			}
			ob_start();
			?>
			<div class="wp-block-wporg-event-list">
			<ul class="wporg-marker-list__container">
				<?php
				foreach ( $event_ids as $event_id ) {
					$event = Translation_Events::get_event_repository()->get_event( $event_id );
					$current_user_attendee_per_event = $attributes['current_user_attendee_per_event'] ?? null;

					?>
				<li class="wporg-marker-list-item">
					<!-- wp:wporg-translate-events-2024/components-event-title 
					<?php
					echo wp_json_encode(
						array(
							'id'        => $event->id(),
							'event_ids' => $event_ids,
							'current_user_attendee_per_event' => $current_user_attendee_per_event,
						)
					);
					?>
																				 /-->
					<!-- wp:wporg-translate-events-2024/components-event-attendance-mode <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
					<!-- wp:wporg-translate-events-2024/components-event-start <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
				</li>
					<?php
				}
				?>
			</ul>
			</div>
			<?php
			return ob_get_clean();
		},
	)
);
