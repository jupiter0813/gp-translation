<?php
/**
 * Events list page.
 */

namespace Wporg\TranslationEvents;

gp_title( __( 'Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
$event_page_title = __( 'Manage Attendees', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );



?>
<div class="event-page-wrapper">
<div class="event-details-stats">
<table>
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Name', 'gp-translation-events' ); ?></th>
			<th><?php esc_html_e( 'Role', 'gp-translation-events' ); ?></th>
			<th><?php esc_html_e( 'Action', 'gp-translation-events' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $attendees as $attendee ) : ?>
			<tr>
				<td>
					<a class="attendee-avatar" href="<?php echo esc_url( get_author_posts_url( $attendee->user_id() ) ); ?>" class="avatar"><?php echo get_avatar( $attendee->user_id(), 48 ); ?></a>
					<a href="<?php echo esc_url( get_author_posts_url( $attendee->user_id() ) ); ?>" class="name"><?php echo esc_html( get_the_author_meta( 'display_name', $attendee->user_id() ) ); ?></a>
				</td>
				<td>
					<?php if ( $attendee->is_host() ) : ?>
						<span>Host</span>
						<?php endif; ?>
				</td>
				<td>
					<a href="">Remove</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>
<?php
gp_tmpl_footer();
