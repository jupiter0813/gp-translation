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
			$view_type = $attributes['view_type'] ?? 'list';
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
			if( $view_type === 'table' ) {
				include_once 'render-list.php';
			} else {
				include_once 'render-table.php';
			}
			return ob_get_clean();
	
		},
	)
);
