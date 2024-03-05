<?php
namespace Wporg\TranslationEvents;

use GP;
?>

<div class="event-list-top-bar">
<h2 class="event-page-title">
	<?php echo esc_html( $event_page_title ); ?>
	<?php if ( isset( $event ) && 'draft' === $event->post_status ) : ?>
				<span class="event-label-draft"><?php echo esc_html( $event->post_status ); ?></span>
			<?php endif; ?>
</h2>
	<ul class="event-list-nav">
		<?php if ( is_user_logged_in() ) : ?>
			<li><a href="<?php echo esc_url( gp_url( '/events/my-events/' ) ); ?>">My Events</a></li>
			<?php
			/**
			 * Filter the ability to create, edit, or delete an event.
			 *
			 * @param bool $can_crud_event Whether the user can create, edit, or delete an event.
			 */
			$can_crud_event = apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->current_user_can( 'admin' ) );
			if ( $can_crud_event ) :
				?>
				<li><a class="button is-primary" href="<?php echo esc_url( gp_url( '/events/new/' ) ); ?>">Create Event</a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<?php if ( isset( $event_id ) ) : ?>
	<p class="event-sub-head">
			<span class="event-host">Host: <a href="<?php echo esc_attr( get_author_posts_url( $event->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $event->post_author ) ); ?></a></span>
			<?php if ( current_user_can( 'edit_post', $event_id ) ) : ?>
				<a class="event-page-edit-link button" href="<?php echo esc_url( gp_url( 'events/edit/' . $event_id ) ); ?>"><span class="dashicons dashicons-edit"></span>Edit event</a>
			<?php endif ?>
		</p>
		<?php endif; ?>

</div>
