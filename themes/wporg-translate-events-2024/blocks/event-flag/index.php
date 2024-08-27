<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/event-flag',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes, $content, $block ) {
			if ( ! isset( $block->context['postId'] ) ) {
				return '';
			}
			$event_id = get_the_ID();
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			if ( ! $event ) {
				return '';
			}
			$current_user_attendee = $block->context['is_user_attending'];
			$event_flag = false;
			if ( $current_user_attendee ) {
				$event_flag = 'Attending';
				if ( $current_user_attendee->is_host() ) {
					$event_flag = 'Host';
				}
			}

			if ( ! $event_flag ) {
				return '';
			}

			ob_start();
			?>
			<span class="my-event-flag"><?php echo esc_html( $event_flag ); ?></span>
			<?php
			return ob_get_clean();
		},
	)
);
