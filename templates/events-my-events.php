<?php
/**
 * Template for My Events.
 */

namespace Wporg\TranslationEvents;

use Wporg\TranslationEvents\Event\Events_Query_Result;
use Wporg\TranslationEvents\Stats\Stats_Calculator;

/** @var Events_Query_Result $events_i_created_query */
/** @var Events_Query_Result $events_i_host_query */
/** @var Events_Query_Result $events_i_attended_query */

gp_title( esc_html__( 'Translation Events', 'gp-translation-events' ) . ' - ' . esc_html__( 'My Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events( array( esc_html__( 'My Events', 'gp-translation-events' ) ) );
gp_tmpl_header();
$event_page_title = __( 'My Events', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>
<div class="events-links-to-anchors">
	<ul>
		<?php if ( ! empty( $events_i_am_or_will_attend_query->events ) ) : ?>
			<li><a href="#events-i-am-or-will-attend"><?php esc_html_e( 'Events I am or will be attending', 'gp-translation-events' ); ?></a></li>
		<?php endif; ?>
		<?php if ( ! empty( $events_i_host_query->events ) ) : ?>
			<li><a href="#events-i-host"><?php esc_html_e( 'Events I host', 'gp-translation-events' ); ?></a></li>
		<?php endif; ?>
		<?php if ( ! empty( $events_i_created_query->events ) ) : ?>
			<li><a href="#events-i-created"><?php esc_html_e( 'Events I have created', 'gp-translation-events' ); ?></a></li>
		<?php endif; ?>
		<?php if ( ! empty( $events_i_attended_query->events ) ) : ?>
			<li><a href="#events-i-attended"><?php esc_html_e( 'Events I attended', 'gp-translation-events' ); ?></a></li>
		<?php endif; ?>
		</ul>
</div>
<div class="event-page-wrapper">
	<?php if ( ! empty( $events_i_am_or_will_attend_query->events ) ) : ?>
		<h2 id="events-i-am-or-will-attend"><?php esc_html_e( 'Events I am or will be attending', 'gp-translation-events' ); ?> </h2>
		<ul>
		<?php
		foreach ( $events_i_am_or_will_attend_query->events as $event ) :
			?>
			<li class="event-list-item">
				<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"><?php echo esc_html( $event->title() ); ?></a>
				<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
					<a href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>" class="button is-small action edit">Edit</a>
				<?php endif; ?>
				<?php if ( 'draft' === $event->status() ) : ?>
					<span class="event-label-<?php echo esc_attr( $event->status() ); ?>"><?php echo esc_html( $event->status() ); ?></span>
				<?php endif; ?>
				<?php if ( $event->start()->format( 'Y-m-d' ) === $event->end()->format( 'Y-m-d' ) ) : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
				<?php else : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
				<?php endif; ?>
				<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_am_or_will_attend_query->page_count,
					'current'   => $events_i_am_or_will_attend_query->current_page,
					'format'    => '?events_i_am_or_will_attend_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	endif;
	?>

	<?php if ( ! empty( $events_i_host_query->events ) ) : ?>
		<h2 id="events-i-host"><?php esc_html_e( 'Events I host', 'gp-translation-events' ); ?> </h2>
		<ul>
		<?php
		foreach ( $events_i_host_query->events as $event ) :
			?>
			<li class="event-list-item">
				<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"><?php echo esc_html( $event->title() ); ?></a>
				<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
					<a href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>" class="button is-small action edit">Edit</a>
				<?php endif; ?>
				<?php if ( 'draft' === $event->status() ) : ?>
					<span class="event-label-<?php echo esc_attr( $event->status() ); ?>"><?php echo esc_html( $event->status() ); ?></span>
				<?php endif; ?>
				<?php if ( $event->start()->format( 'Y-m-d' ) === $event->end()->format( 'Y-m-d' ) ) : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
				<?php else : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
				<?php endif; ?>
				<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_host_query->page_count,
					'current'   => $events_i_host_query->current_page,
					'format'    => '?events_i_hosted_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	endif;
	?>

	<?php if ( ! empty( $events_i_created_query->events ) ) : ?>
		<h2 id="events-i-created"><?php esc_html_e( 'Events I have created', 'gp-translation-events' ); ?> </h2>
		<ul>
			<?php
			foreach ( $events_i_created_query->events as $event ) :
				?>
				<li class="event-list-item">
					<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"><?php echo esc_html( $event->title() ); ?></a>
					<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
						<a href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>" class="button is-small action edit">Edit</a>
					<?php endif; ?>
					<?php if ( 'draft' === $event->status() ) : ?>
						<span class="event-label-<?php echo esc_attr( $event->status() ); ?>"><?php echo esc_html( $event->status() ); ?></span>
					<?php endif; ?>
					<?php if ( $event->start()->format( 'Y-m-d' ) === $event->end()->format( 'Y-m-d' ) ) : ?>
						<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
					<?php else : ?>
						<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
					<?php endif; ?>
					<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_created_query->page_count,
					'current'   => $events_i_created_query->current_page,
					'format'    => '?events_i_created_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	endif;
	?>

	<?php if ( ! empty( $events_i_attended_query->events ) ) : ?>
		<h2 id="events-i-attended"><?php esc_html_e( 'Events I attended', 'gp-translation-events' ); ?> </h2>
		<ul>
		<?php foreach ( $events_i_attended_query->events as $event ) : ?>
			<li class="event-list-item">
				<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"><?php echo esc_html( $event->title() ); ?></a>
				<?php if ( $event->start() === $event->end() ) : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
				<?php else : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
				<?php endif; ?>
				<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_attended_query->page_count,
					'current'   => $events_i_attended_query->current_page,
					'format'    => '?events_i_attended_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	else :
		echo 'No events found.';
	endif;
	?>
</div>
<?php
	gp_tmpl_footer();
?>
