<?php
/**
 * Events list page.
 */

namespace Wporg\TranslationEvents;

use DateTime;
use WP_Query;

/** @var WP_Query $current_events_query */
/** @var WP_Query $upcoming_events_query */

gp_title( __( 'Translation Events' ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<h2 class="event_page_title">Translation Events</h2>
<div class="event-left-col">
<?php
if ( $current_events_query->have_posts() ) :
	?>
	<h3>Current events</h3>
	<ul>
		<?php
		while ( $current_events_query->have_posts() ) :
			$current_events_query->the_post();
			$event_start = ( new DateTime( get_post_meta( get_the_ID(), '_event_start', true ) ) )->format( 'l, F j, Y' );
			$event_url   = gp_url( wp_make_link_relative( get_the_permalink() ) );
			?>
			<li class="event-list-item">
				<span class="event-list-date"><?php echo esc_html( $event_start ); ?></span>
				<a href="<?php echo esc_url( $event_url ); ?>"><?php the_title(); ?></a> by <span><?php the_author(); ?></span>
				<p><?php the_excerpt(); ?></p>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $current_events_query->max_num_pages,
				'current'   => max( 1, $current_events_query->query_vars['current_events_paged'] ),
				'format'    => '?current_events_paged=%#%',
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);

	wp_reset_postdata();
endif;
if ( $upcoming_events_query->have_posts() ) :
	?>
	<h3>Upcoming events</h3>
	<ul>
		<?php
		while ( $upcoming_events_query->have_posts() ) :
			$upcoming_events_query->the_post();
			$event_start = ( new DateTime( get_post_meta( get_the_ID(), '_event_start', true ) ) )->format( 'l, F j, Y' );
			?>
			<li class="event-list-item">
				<span class="event-list-date"><?php echo esc_html( $event_start ); ?></span>
				<a href="<?php echo esc_url( gp_url( wp_make_link_relative( get_the_permalink() ) ) ); ?>"><?php the_title(); ?></a> by <span><?php the_author(); ?></span>
				<p><?php the_excerpt(); ?></p>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $upcoming_events_query->max_num_pages,
				'current'   => max( 1, $upcoming_events_query->query_vars['upcoming_events_paged'] ),
				'format'    => '?upcoming_events_paged=%#%',
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);

	wp_reset_postdata();
endif;

if ( 0 === $current_events_query->post_count && 0 === $upcoming_events_query->post_count ) :
	echo 'No events found.';
endif;
?>
</div>
<?php if ( is_user_logged_in() ) : ?>
	<div class="event-right-col">
		<h3 class="">Events I'm Attending</h3>
		<?php if ( ! $user_attending_events_query->have_posts() ) : ?>
			<p>You don't have any events to attend.</p>
		<?php else : ?>
			<ul class="event-attending-list">
				<?php
				while ( $user_attending_events_query->have_posts() ) :
					$user_attending_events_query->the_post();
					$event_start = ( new DateTime( get_post_meta( get_the_ID(), '_event_start', true ) ) )->format( 'M j, Y' );
					$event_end   = ( new DateTime( get_post_meta( get_the_ID(), '_event_end', true ) ) )->format( 'M j, Y' );
					?>
					<li class="event-list-item">
						<a href="<?php echo esc_url( gp_url( wp_make_link_relative( get_the_permalink() ) ) ); ?>"><?php the_title(); ?></a>
						<?php if ( $event_start === $event_end ) : ?>
							<span class="event-list-date events-i-am-attending"><?php echo esc_html( $event_start ); ?></span>
						<?php else : ?>
							<span class="event-list-date events-i-am-attending"><?php echo esc_html( $event_start ); ?> - <?php echo esc_html( $event_end ); ?></span>
						<?php endif; ?>
					</li>
					<?php
				endwhile;
				?>
			</ul>
			<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $user_attending_events_query->max_num_pages,
							'current'   => max( 1, $user_attending_events_query->query_vars['user_attending_events_paged'] ),
							'format'    => '?user_attending_events_paged=%#%',
							'prev_text' => '&laquo; Previous',
							'next_text' => 'Next &raquo;',
						)
					) ?? ''
				);

				wp_reset_postdata();
		endif;
		?>
	</div>
<?php endif; ?>
