<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

$data = array(
	'event_ids' => $event_ids,
	'show_flag' => true,
);
?>
<div class="wp-block-wporg-event-list">
	<ul class="wporg-marker-list__container">
		<?php
		foreach ( $event_ids as $event_id ) {
			?>
			<li class="wporg-marker-list-item">
				<!-- wp:wporg-translate-events-2024/event-template <?php echo wp_json_encode( array( 'id' => $event_id ) ); ?> -->
				<div>
					<!-- wp:wporg-translate-events-2024/event-title /-->
					<!-- wp:wporg-translate-events-2024/event-flag /-->
				</div>
				<!-- wp:wporg-translate-events-2024/event-attendance-mode /-->
				<!-- wp:wporg-translate-events-2024/event-start /-->
				<!-- /wp:wporg-translate-events-2024/event-list-->

				<!-- /wp:wporg-translate-events-2024/event-template -->
			</li>
			<?php
		}
		?>
	</ul>
</div>
