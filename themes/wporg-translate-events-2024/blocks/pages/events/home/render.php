<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

$current_events_data = array(
	'event_ids' => $attributes['current_events_query']['event_ids'] ?? array(),
);

$upcoming_events_data = array(
	'event_ids' => $attributes['upcoming_events_query']['event_ids'] ?? array(),
);

$past_events_data = array(
	'event_ids' => $attributes['past_events_query']['event_ids'] ?? array(),
);

?>
<!-- wp:pattern {"slug":"wporg-translate-events-2024/front-cover"} /-->
<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter"} -->
<h2 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);">Your next events</h2>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $current_events_data ); ?>  /-->
<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter","spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}} -->
<h2 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);">Upcoming events</h2>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $upcoming_events_data ); ?>  /-->
<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter"} -->
<h2 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);">Past events</h2>
<!-- /wp:heading -->
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $past_events_data ); ?>  /-->
