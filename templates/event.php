<?php
/**
 * Template for event page.
 */

/** @var WP_Post $event */
/** @var int $event_id */
/** @var string $event_title */
/** @var string $event_description */
/** @var string $event_start */
/** @var string $event_end */
/** @var bool $user_is_attending */
/** @var WPORG_GP_Translation_Events_Event_Stats $event_stats */

/* translators: %s: Event title. */
gp_title( sprintf( __( 'Translation Events - %s' ), esc_html( $event_title ) ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<div class="event-details-page">
	<div class="event-details-head">
		<h1>
			<?php echo esc_html( $event_title ); ?>
			<?php if ( 'draft' === $event->post_status ) : ?>
				<span class="event-label-draft"><?php echo esc_html( $event->post_status ); ?></span>
			<?php endif; ?>
		</h1>
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
		<?php if ( is_user_logged_in() ) : ?>
		<div class="event-details-join">
			<form class="event-details-attend" method="post" action="<?php echo esc_url( gp_url( "/events/attend/$event_id" ) ); ?>">
				<?php if ( ! $user_is_attending ) : ?>
					<input type="submit" class="button is-primary" value="Attend Event"/>
				<?php else : ?>
					<input type="submit" class="button is-secondary" value="You're attending"/>
				<?php endif ?>
			</form>
		</div>
		<?php endif; ?>
	</div>
<?php if ( ! empty( $event_stats->rows() ) ) : ?>
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
		<?php /** @var $row WPORG_GP_Translation_Events_Stats_Row */ ?>
		<?php foreach ( $event_stats->rows() as $locale_ => $row ) : ?>
			<tr>
				<td><?php echo esc_html( $locale_ ); ?></td>
				<td><?php echo esc_html( $row->created ); ?></td>
				<td><?php echo esc_html( $row->reviewed ); ?></td>
				<td><?php echo esc_html( $row->users ); ?></td>
			</tr>
		<?php endforeach ?>
			<tr class="event-details-stats-totals">
				<td>Total</td>
				<td><?php echo esc_html( $event_stats->totals()->created ); ?></td>
				<td><?php echo esc_html( $event_stats->totals()->reviewed ); ?></td>
				<td><?php echo esc_html( $event_stats->totals()->users ); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
<?php endif ?>
</div>
