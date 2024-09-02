<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Attendee\Attendee;

$event     = $attributes['event'];
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
<button type="submit" class="wp-block-button__link">Pledge to attend</button>

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
<!-- wp:paragraph -->
<?php echo wp_kses_post( wpautop( make_clickable( $event->description() ) ) ); ?>
<!-- /wp:paragraph -->
<?php if ( ! empty( $contributors ) ) : ?>
<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium","fontFamily":"inter"} -->
<h4 class="wp-block-heading has-inter-font-family has-medium-font-size" style="font-style:normal;font-weight:700"><?php echo esc_html( sprintf( __( 'Contributors (%d)', 'wporg-translate-events-2024' ), number_format_i18n( count( $contributors ) ) ) ); ?></h4>
	<?php
	// translators: %d is the number of contributors.
	echo esc_html( sprintf( __( 'Contributors (%d)', 'gp-translation-events' ), number_format_i18n( count( $contributors ) ) ) );
	?>
<!-- /wp:heading -->
<?php endif; ?>
