<?php
global $translation_events;
global $translation_events_lookup;
ob_start();
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
<?php
$content = ob_get_clean();

register_block_pattern(
	'wporg-translate-events-2024/events-list',
	array(
		'title'      => __( 'Events List', 'wporg-translate-events-2024' ),
		'categories' => array( 'featured' ),
		'content'    => $content,
	)
);
