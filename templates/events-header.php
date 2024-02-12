<div class="event-list-top-bar">
	<ul class="event-list-nav">
		<?php if ( is_user_logged_in() ) : ?>
			<li><a href="<?php echo esc_url( gp_url( '/events/my-events/' ) ); ?>">My Events</a></li>
			<li><a class="button is-primary" href="<?php echo esc_url( gp_url( '/events/new/' ) ); ?>">Create Event</a></li>
		<?php endif; ?>
	</ul>
</div>
