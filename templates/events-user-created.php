<?php
/**
 * Template for My Events.
 */

namespace Wporg\TranslationEvents;

use WP_Query;

/** @var WP_Query $query */

gp_title( __( 'Translation Events' ) . ' - ' . esc_html__( 'My Events' ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<h2 class="event_page_title">My Events</h2>
<div>
<?php if ( $query->have_posts() ) : ?>
	<ul>
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$event_id                      = get_the_ID();
		$event_start                   = get_post_meta( $event_id, '_event_start', true );
		list( $permalink, $post_name ) = get_sample_permalink( $event_id );
		$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
		$event_url                     = gp_url( wp_make_link_relative( $permalink ) );
		$event_edit_url                = gp_url( 'events/edit/' . $event_id );
		$event_status                  = get_post_status( $event_id );
		?>
		<li class="event-list-item">
			<a class="event-link-<?php echo esc_attr( $event_status ); ?>" href="<?php echo esc_url( $event_url ); ?>"><?php the_title(); ?></a>
			<a href="<?php echo esc_url( $event_edit_url ); ?>" class="button is-small action edit">Edit</a>
			<?php if ( 'draft' === $event_status ) : ?>
				<span class="event-label-<?php echo esc_attr( $event_status ); ?>"><?php echo esc_html( $event_status ); ?></span>
			<?php endif; ?>
			<p><?php the_excerpt(); ?></p>
		</li>
	<?php endwhile; ?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $query->max_num_pages,
				'current'   => max( 1, get_query_var( 'page' ) ),
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
				'format'    => '?page=%#%',
			)
		) ?? ''
	);

	wp_reset_postdata();
else :
	echo 'No events found.';
endif;
?>
</div>
