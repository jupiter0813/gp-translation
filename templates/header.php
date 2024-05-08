<?php

use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Urls;

/** @var string $html_title */
/** @var string|callable $page_title */
/** @var string[] $breadcrumbs */
/** @var WP_User $user */

/** @var Event $event */
/** @var Attendee[] $hosts */

gp_title( $html_title );
gp_breadcrumb_translation_events( $breadcrumbs );
gp_tmpl_header();
?>

<div class="event-list-top-bar">
	<h2 class="event-page-title">
		<?php if ( is_callable( $page_title ) ) : ?>
			<?php $page_title(); ?>
		<?php else : ?>
			<?php echo esc_html( $page_title ); ?>
		<?php endif; ?>
	</h2>

	<ul class="event-list-nav">
		<?php if ( is_user_logged_in() ) : ?>
			<?php if ( current_user_can( 'manage_translation_events' ) ) : ?>
				<li><a href="<?php echo esc_url( Urls::events_trashed() ); ?>">Deleted Events</a></li>
			<?php endif; ?>
			<li><a href="<?php echo esc_url( Urls::my_events() ); ?>">My Events</a></li>
			<?php if ( current_user_can( 'create_translation_event' ) ) : ?>
				<li><a class="button is-primary" href="<?php echo esc_url( Urls::event_create() ); ?>">Create Event</a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>

	<?php if ( isset( $event ) && ! isset( $event_form_name ) ) : ?>
		<p class="event-sub-head">
			<span class="event-host">
				<?php
				if ( isset( $hosts ) ) :
					if ( count( $hosts ) > 0 ) :
						if ( 1 === count( $hosts ) ) :
							esc_html_e( 'Host:', 'gp-translation-events' );
						else :
							esc_html_e( 'Hosts:', 'gp-translation-events' );
						endif;
					else :
						esc_html_e( 'Created by:', 'gp-translation-events' );
						?>
						&nbsp;<a href="<?php echo esc_attr( get_author_posts_url( $user->ID ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $user->ID ) ); ?></a>
					<?php endif; ?>
					<?php foreach ( $hosts as $host ) : ?>
						&nbsp;<a href="<?php echo esc_attr( get_author_posts_url( $host->user_id() ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $host->user_id() ) ); ?></a>
						<?php if ( end( $hosts ) !== $host ) : ?>
						,
						<?php else : ?>
						.
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</span>
			<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
				<a class="event-page-edit-link" href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Edit event', 'gp-translation-events' ); ?></a>
			<?php elseif ( current_user_can( 'edit_translation_event_attendees', $event->id() ) ) : ?>
				<a class="event-page-attendees-link" href="<?php echo esc_url( Urls::event_attendees( $event->id() ) ); ?>"><?php esc_html_e( 'Manage attendees', 'gp-translation-events' ); ?></a>
			<?php endif ?>
		</p>
	<?php endif; ?>
</div>
