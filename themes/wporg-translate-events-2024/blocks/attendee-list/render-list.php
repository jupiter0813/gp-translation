<?php

if ( empty( $attendees_not_contributing ) || ! current_user_can( 'edit_translation_event', $event_id ) ) {
	return;
}

?>
<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter"} -->
<h4 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700"><?php echo esc_html( sprintf( __( 'Attendees (%d)', 'wporg-translate-events-2024' ), number_format_i18n( count( $attendees ) ) ) ); ?></h4>
<!-- /wp:heading -->
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
	<!-- wp:columns {"columns":3} -->
	<div class="wp-block-columns has-3-columns">
<?php
$columns = 3;
$counter = 0;

foreach ( $attendees_not_contributing as $attendee ) :
	if ( $columns === $counter ) :
		?>
		</div><!-- /wp:columns -->
		<!-- wp:columns {"columns":3} --><div class="wp-block-columns has-3-columns">
		<?php
	endif;
	?>
	<!-- wp:wporg-translate-events-2024/attendee-avatar-name 
	<?php
	echo wp_json_encode(
		array(
			'user_id'            => $attendee->user_id(),
			'is_new_contributor' => $attendee->is_new_contributor(),
		)
	);
	?>
	/-->
	<!-- wp:column -->
	<div class="wp-block-column">
	</div>
	<!-- /wp:column -->
	<?php
	$counter++;
endforeach;
?>
</div><!-- /wp:columns -->
</div><!-- /wp:group -->
