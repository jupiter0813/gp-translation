<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

foreach ( $event_ids as $event_id ) {
	?>
	<!-- wp:wporg-translate-events-2024/title <?php echo wp_json_encode( array( 'id' => $event_id ) ); ?> /-->
	<!-- wp:wporg-translate-events-2024/start-date <?php echo wp_json_encode( array( 'id' => $event_id ) ); ?> /-->
	<!-- wp:wporg-translate-events-2024/end-date <?php echo wp_json_encode( array( 'id' => $event_id ) ); ?> /-->
	<!-- wp:wporg-translate-events-2024/excerpt <?php echo wp_json_encode( array( 'id' => $event_id ) ); ?> /-->
	<?php
}
?>
