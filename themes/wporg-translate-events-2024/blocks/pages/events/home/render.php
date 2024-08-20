<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

$current_events_data = array(
	'event_ids' => $attributes['current_events_query']['event_ids'] ?? array(),
	'show_flag' => false,
);

$upcoming_events_data = array(
	'event_ids' => $attributes['upcoming_events_query']['event_ids'] ?? array(),
	'show_flag' => false,
);

$past_events_data = array(
	'event_ids' => $attributes['past_events_query']['event_ids'] ?? array(),
	'show_flag' => false,
);

?>
<!-- wp:pattern {"slug":"wporg-translate-events-2024/front-cover"} /-->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Your next events</h4>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $current_events_data ); ?>  /-->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Upcoming events</h4>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $upcoming_events_data ); ?>  /-->
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Past events</h4>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $past_events_data ); ?>  /-->