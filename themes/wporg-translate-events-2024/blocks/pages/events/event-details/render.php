<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Attendee\Attendee;

$event               = $attributes['event'];
$user_is_attending   = $attributes['user_is_attending'];
$user_is_contributor = $attributes['user_is_contributor'];

$has_hosts = count( $hosts ) > 0;

if ( ! $has_hosts ) {
	$hosts = array( new Attendee( $event->id(), $event->author_id(), true ) );
}
$hosts_list = array_map(
	function ( $host ) {
		$url  = get_author_posts_url( $host->user_id() );
		$name = get_the_author_meta( 'display_name', $host->user_id() );
		return '<a href="' . esc_attr( $url ) . '">' . esc_html( $name ) . '</a>';
	},
	$hosts
);

if ( ! $has_hosts ) {
	/* translators: %s: Display name of the user who created the event. */
	$hosts_string = __( 'Created by: %s', 'gp-translation-events' );
} else {
	/* translators: %s is a comma-separated list of event hosts (=usernames) */
	$hosts_string = _n( 'Host: %s', 'Hosts: %s', count( $hosts ), 'gp-translation-events' );
}
?>
<!-- wp:wporg-translate-events-2024/event-attend-button <?php echo wp_json_encode( array( 'id' => $event->id(), 'user_is_attending' => $user_is_attending, 'user_is_contributor' => $user_is_contributor ) ); ?> /-->

<!-- wp:paragraph -->
<p>
<?php
echo wp_kses(
	sprintf( $hosts_string, implode( ', ', $hosts_list ) ),
	array( 'a' => array( 'href' => array() ) )
);
?>
<span> Date: <strong><?php $event->start()->print_time_html(); ?></strong></span>
</p>
<!-- /wp:paragraph -->

<!-- wp:wporg-translate-events-2024/event-description <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/attendee-list <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/contributor-list <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/event-stats <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/event-projects <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/event-contribution-summary <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
