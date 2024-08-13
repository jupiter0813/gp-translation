<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;


register_block_type(
	'wporg-translate-events-2024/event-list',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event_ids = $attributes['event_ids'] ?? array();

			if ( empty( $event_ids ) ) {
				return;
			}

			$user_id = get_current_user_id();
			$current_user_attendee_per_event = array();
			if ( isset( $attributes['show_flag'] ) && $attributes['show_flag'] ) {
				$current_user_attendee_per_event = Translation_Events::get_attendee_repository()->get_attendees_for_events_for_user( $event_ids, $user_id );
			}

			ob_start();
			?>
			<div class="wp-block-wporg-event-list">
			<ul class="wporg-marker-list__container">
				<?php
				foreach ( $event_ids as $event_id ) {
					$event = Translation_Events::get_event_repository()->get_event( $event_id );
					$current_user_attendee = $current_user_attendee_per_event[ $event_id ] ?? null;

					if ( $current_user_attendee ) {
						$event_flag = 'Attending';
						if ( $current_user_attendee->is_host() ) {
							$event_flag = 'Host';
						}
					}

					?>
				<li class="wporg-marker-list-item">
					<!-- wp:wporg-translate-events-2024/event-title 
						<?php
						echo wp_json_encode(
							array(
								'url'   => Urls::event_details( $event->id() ),
								'title' => $event->title(),
								'flag'  => $event_flag,
							)
						);
						?>
					/-->
					<!-- wp:wporg-translate-events-2024/event-attendance-mode <?php echo wp_json_encode( array( 'attendance_mode' => $event->attendance_mode() ) ); ?> /-->
					<!-- wp:wporg-translate-events-2024/event-start <?php echo wp_json_encode( array( 'date' => $event->start()->format( 'F j, Y' ) ) ); ?> /-->
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
