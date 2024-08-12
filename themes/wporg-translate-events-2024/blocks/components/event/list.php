<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;


register_block_type(
	'wporg-translate-events-2024/component-event-list',
	array(
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
								'url'   => Urls::event_details( $event->id() ),
								'title' => $event->title(),
							)
						);
						?>
					/-->
					<!-- wp:wporg-translate-events-2024/components-event-attendance-mode <?php echo wp_json_encode( array( 'attendance_mode' => $event->attendance_mode() ) ); ?> /-->
					<!-- wp:wporg-translate-events-2024/components-event-start <?php echo wp_json_encode( array( 'date' => $event->start()->format( 'F j, Y' ) ) ); ?> /-->
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
