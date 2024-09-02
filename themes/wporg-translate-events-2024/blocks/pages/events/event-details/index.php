<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/page-events-event-details',
	array(
		'render_callback' => function ( array $attributes ) {
			add_filter(
				'wporg_block_site_breadcrumbs',
				function ( $breadcrumbs ): array {
					return array_merge(
						$breadcrumbs,
						array(
							array(
								'title' => __( 'Event', 'wporg-translate-events-2024' ),
								'url'   => null,
							),
						)
					);
				}
			);

			$event_id = $attributes['event_id'] ?? array();
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			if ( ! $event ) {
				return '';
			}
			$attributes['event'] = $event;

			render_page(
				__DIR__ . '/render.php',
				esc_html( $event->title() ),
				$attributes
			);
		},
	)
);
