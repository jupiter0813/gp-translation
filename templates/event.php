<?php

/** @var WP_Post $event */
/** @var string $event_title */
/** @var string $event_description */
/** @var string $event_start_date */
/** @var WPORG_GP_Translation_Events_Event_Stats $event_stats */

/* translators: %s: Event title. */
gp_title( sprintf( __( 'Translation Events - %s' ), esc_html( $event_title ) ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), dirname( __FILE__ ) );

?>

<div class="event-details-page">
	<div class="event-details-head">
		<h1><?php echo esc_html( $event_title ); ?></h1>
		<p>Host: <a href="<?php echo esc_attr( get_author_posts_url( $event->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $event->post_author ) ); ?></a></p>
	</div>
	<div class="event-details-left">
		<div class="event-page-content">
			<?php echo esc_html( $event_description ); ?>
		</div>
	</div>
	<div class="event-details-right">
		<div class="event-details-date">
			<p><span class="dashicons dashicons-clock"></span> <time class="event-utc-time" datetime="<?php echo esc_attr( $event_start ); ?>"></time> - <time class="event-utc-time" datetime="<?php echo esc_attr( $event_end ); ?>"></time></p>
		</div>
		<div class="event-details-join">
			<button class="button is-primary" id="join-event">Attend Event</button>
		</div>
	</div>
<?php if ( ! empty( $event_stats->rows() ) ):?>
	<div class="event-details-stats">
		<h2>Stats</h2>
		<table>
			<thead>
			<tr>
				<th scope="col">Locale</th>
				<th scope="col">Translations created</th>
				<th scope="col">Translations reviewed</th>
				<th scope="col">Contributors</th>
			</tr>
			</thead>
			<tbody>
			<?php /** @var $row WPORG_GP_Translation_Events_Stats_Row */?>
		<?php foreach ( $event_stats->rows() as $locale => $row ): ?>
			<tr>
				<td><?php echo esc_html( $locale ) ?></td>
				<td><?php echo esc_html( $row->created ) ?></td>
				<td><?php echo esc_html( $row->reviewed ) ?></td>
				<td><?php echo esc_html( $row->users ) ?></td>
			</tr>
		<?php endforeach ?>
			<tr class="event-details-stats-totals">
				<td>Total</td>
				<td><?php echo esc_html( $event_stats->totals()->created ) ?></td>
				<td><?php echo esc_html( $event_stats->totals()->reviewed ) ?></td>
				<td><?php echo esc_html( $event_stats->totals()->users ) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
<?php endif ?>
</div>
