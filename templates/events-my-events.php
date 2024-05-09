<?php
/**
 * Template for My Events.
 */
namespace Wporg\TranslationEvents\Templates;

use Wporg\TranslationEvents\Event\Events_Query_Result;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var Events_Query_Result $events_i_created_query */
/** @var Events_Query_Result $events_i_host_query */
/** @var Events_Query_Result $events_i_attended_query */

Templates::header(
	array(
		'html_title'  => esc_html__( 'Translation Events', 'gp-translation-events' ) . ' - ' . esc_html__( 'My Events', 'gp-translation-events' ),
		'page_title'  => __( 'My Events', 'gp-translation-events' ),
		'breadcrumbs' => array( esc_html__( 'My Events', 'gp-translation-events' ) ),
	),
);
?>

<div class="event-page-wrapper">
	<?php if ( ! empty( $events_i_host_query->events ) ) : ?>
		<h2><?php esc_html_e( 'Events I host', 'gp-translation-events' ); ?> </h2>
		<?php
		Templates::partial(
			'event-list',
			array(
				'query'                  => $events_i_host_query,
				'pagination_query_param' => 'events_i_hosted_paged',
				'show_start'             => true,
				'show_end'               => true,
				'relative_time'          => false,
			),
		);
	endif;
	?>

	<?php if ( ! empty( $events_i_created_query->events ) ) : ?>
		<h2><?php esc_html_e( 'Events I have created', 'gp-translation-events' ); ?> </h2>
		<?php
		Templates::partial(
			'event-list',
			array(
				'query'                  => $events_i_created_query,
				'pagination_query_param' => 'events_i_created_paged',
				'show_start'             => true,
				'show_end'               => true,
				'relative_time'          => false,
			),
		);
	endif;
	?>

	<h2><?php esc_html_e( 'Events I attended', 'gp-translation-events' ); ?> </h2>
	<?php if ( ! empty( $events_i_attended_query->events ) ) : ?>
		<?php
		Templates::partial(
			'event-list',
			array(
				'query'                  => $events_i_attended_query,
				'pagination_query_param' => 'events_i_attended_paged',
				'show_start'             => true,
				'show_end'               => true,
				'relative_time'          => false,
			),
		);
	else :
		echo 'No events found.';
	endif;
	?>
</div>

<?php Templates::footer(); ?>
