<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
use Wporg\TranslationEvents\Event\Event_Start_Date;
use DateTimeZone;
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	
	<?php
		$_event_start = get_post_meta( get_the_ID(), '_event_start', true );
		$utc          = new DateTimeZone( 'UTC' );
		$start_date   = new Event_Start_Date( $_event_start, $utc );

	if ( ! empty( $_event_start ) ) {
		echo esc_html( $start_date );
	} else {
		esc_html_e( 'No start date set', 'start-date' );
	}
	?>
</p>
