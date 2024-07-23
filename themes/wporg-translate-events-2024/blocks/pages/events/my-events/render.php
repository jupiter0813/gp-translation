<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();

global $translation_events;
global $translation_events_lookup;
?>
<div class="wp-block-wporg-event-list">
<ul class="wporg-marker-list__container">
	<?php
	foreach ( $translation_events->events as $event ) {
		$translation_events_lookup[ $event->id() ] = $event;
		?>
	
	<li class="wporg-marker-list-item">
		<!-- wp:wporg-translate-events-2024/components-event-title <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
		<!-- wp:wporg-translate-events-2024/components-event-attendance-mode <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
		<!-- wp:wporg-translate-events-2024/components-event-start <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
	</li>
	
		<?php
	}
	?>
</ul>
</div>
