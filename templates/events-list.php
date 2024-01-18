<?php
gp_title( __( 'Translation Events' ) );
gp_tmpl_header();

?>

<h2 class="event_page_title">Upcoming Translation Events</h2>
<?php


$_paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args   = array(
	'post_type'      => 'event',
	'posts_per_page' => 10,
	'paged'          => $_paged,
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) :
	?>
	<ul>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
			<li>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> by <span><?php the_author(); ?></span>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'prev_text' => '&laquo; Previous',
			'next_text' => 'Next &raquo;',
		)
	);

	wp_reset_postdata();
else :
	echo 'No events found.';
endif;
?>
