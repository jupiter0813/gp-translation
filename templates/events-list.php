<?php
gp_title( __( 'Translation Events' ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), dirname( __FILE__ ) );
?>


<h2 class="event_page_title">Upcoming Translation Events</h2>
<div class="event-left-col">
<?php
if ( $query->have_posts() ) :
	?>
	<ul>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$event_start = ( new DateTime( get_post_meta( get_the_ID(), '_event_start', true ) ) )->format('l, F j, Y');
			?>
			<li class="event-list-item">
				<span class="event-list-date"><?php echo esc_html( $event_start ); ?></span>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> by <span><?php the_author(); ?></span>
				<p><?php the_excerpt(); ?></p>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo esc_html( paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'prev_text' => '&laquo; Previous',
			'next_text' => 'Next &raquo;',
		)
	) );

	wp_reset_postdata();
else :
	echo 'No events found.';
endif;
?>
</div>
<div class="event-right-col">
	<h3 class="">Events I'm Attending</h3>
	<?php
		// TODO: Add the list of events the user is attending.
	?>
	<ul class="event-attending-list">
		<li>
			<a href="#">Spanish Translation Day</a>
		</li>
		<li>
			<a href="#">Let's Translate 2024</a>
		</li>
		<li>
			<a href="#">Basics of Translation Workshop</a>
		</li>
	</ul>
</div>
