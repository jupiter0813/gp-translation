<?php
/**
 * Events list page.
 */
namespace Wporg\TranslationEvents\Templates;

use Wporg\TranslationEvents\Event\Events_Query_Result;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var Events_Query_Result $current_events_query */
/** @var Events_Query_Result $upcoming_events_query */
/** @var Events_Query_Result $past_events_query */
/** @var Events_Query_Result $user_attending_events_query */

Templates::header(
	array(
		'html_title' => __( 'Translation Events', 'gp-translation-events' ),
		'page_title' => __( 'Translation Events', 'gp-translation-events' ),
	),
);
?>

<div class="event-page-wrapper">
<div class="event-left-col">
<?php
if ( empty( $current_events_query->events ) && empty( $upcoming_events_query->events ) && empty( $past_events_query->post_count ) ) :
	esc_html_e( 'No events found.', 'gp-translation-events' );
endif;

if ( ! empty( $current_events_query->events ) ) :
	?>
	<h2><?php esc_html_e( 'Current events', 'gp-translation-events' ); ?></h2>
	<?php
	Templates::partial(
		'event-list',
		array(
			'query'                  => $current_events_query,
			'pagination_query_param' => 'current_events_paged',
			'show_end'               => true,
		),
	);
endif;

if ( ! empty( $upcoming_events_query->events ) ) :
	?>
	<h2><?php esc_html_e( 'Upcoming events', 'gp-translation-events' ); ?></h2>
	<?php
	Templates::partial(
		'event-list',
		array(
			'query'                  => $upcoming_events_query,
			'pagination_query_param' => 'upcoming_events_paged',
			'show_start'             => true,
		),
	);
endif;

if ( ! empty( $past_events_query->events ) ) :
	?>
	<h2><?php esc_html_e( 'Past events', 'gp-translation-events' ); ?></h2>
	<?php
	Templates::partial(
		'event-list',
		array(
			'query'                  => $past_events_query,
			'pagination_query_param' => 'past_events_paged',
			'show_end'               => true,
		),
	);
endif;
?>

</div>
<?php if ( is_user_logged_in() ) : ?>
	<div class="event-right-col">
		<h2>Events I'm Attending</h2>
		<?php if ( empty( $user_attending_events_query->events ) ) : ?>
			<p>You don't have any events to attend.</p>
		<?php else : ?>
			<ul class="event-attending-list">
				<?php foreach ( $user_attending_events_query->events as $event ) : ?>
					<li class="event-list-item">
						<a href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"><?php echo esc_html( $event->title() ); ?></a>
						<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html( 'F j, Y H:i T' ); ?> - <?php $event->end()->print_time_html( 'F j, Y H:i T' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $user_attending_events_query->page_count,
							'current'   => $user_attending_events_query->current_page,
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
</div>

<?php Templates::footer(); ?>
