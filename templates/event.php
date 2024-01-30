<?php

/** @var WP_Post $event */
/** @var string $event_title */
/** @var string $event_description */
/** @var string $event_start_date */
/** @var WPORG_GP_Translation_Events_Event_Stats $event_stats */

/* translators: %s: Event title. */
gp_title( sprintf( __( 'Translation Events - %s' ), esc_html( $event_title ) ) );
gp_tmpl_header();
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
			<p><span class="dashicons dashicons-calendar"></span> <?php echo esc_html( ( new DateTime( $event_start_date ) )->format( 'l, F j, Y' ) ); ?></p>
			<p><span class="dashicons dashicons-clock"></span> 13:00 - 15:00</p>
		</div>
		<div class="event-details-join">
			<button class="button is-primary" id="join-event">Attend Event</button>
		</div>
	</div>
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
			<?php foreach ( $event_stats->by_locale as $locale => $stats ): ?>
			<tr>
				<td><?php echo esc_html( $locale ) ?></td>
				<td><?php echo esc_html( $stats->created()->value() ) ?></td>
				<td><?php echo esc_html( $stats->reviewed()->value() ) ?></td>
				<td><?php echo esc_html( $stats->users()->value() ) ?></td>
			</tr>
			<?php endforeach ?>
			<tr class="event-details-stats-totals">
				<td>Total</td>
				<td><?php echo esc_html( $event_stats->totals->created()->value() ) ?></td>
				<td><?php echo esc_html( $event_stats->totals->reviewed()->value() ) ?></td>
				<td><?php echo esc_html( $event_stats->totals->users()->value() ) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
