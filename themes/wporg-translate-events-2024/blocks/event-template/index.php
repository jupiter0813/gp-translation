<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/event-template',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes, $content, $block ) {
			if ( ! isset( $attributes['id'] ) ) {
				return '';
			}

			$query = new \WP_Query(
				array(
					'p'         => intval( $attributes['id'] ),
					'post_type' => Translation_Events::CPT,
				)
			);
			$event_ids = $attributes['event_ids'];
			$event_id = $attributes['id'];
			$user_id = get_current_user_id();
			static $cached_current_user_attendee = [];
			
			$current_user_attendee = function(  $event_ids, $user_id ) use ( &$cached_current_user_attendee ) {

				if ( ! isset( $cached_current_user_attendee[ $user_id ] ) ) {
					$cached_current_user_attendee[ $user_id ] = Translation_Events::get_attendee_repository()->get_attendees_for_events_for_user( $event_ids, $user_id );
				}

				return $cached_current_user_attendee[ $user_id ];
			};
			$is_attending = $current_user_attendee($event_ids, $user_id )[$event_id] ?? null;

			$block_content = '';
			while ( $query->have_posts() ) {
				$query->the_post();
				$block_instance = $block->parsed_block;
				$filter_block_context = static function ( $context ) use ( $attributes, $is_attending ) {
					$context['postId'] = $attributes['id'];
					$context['postType'] = Translation_Events::CPT;
					$context['is_user_attending']= $is_attending;
					return $context;
				};

				// Use an early priority to so that other 'render_block_context' filters have access to the values.
				add_filter( 'render_block_context', $filter_block_context, 1 );
				$block_content = ( new \WP_Block( $block_instance ) )->render( array( 'dynamic' => false ) );
				remove_filter( 'render_block_context', $filter_block_context, 1 );
			}
			wp_reset_postdata();
			return $block_content;
		},
	)
);
