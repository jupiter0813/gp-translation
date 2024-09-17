<?php
use Wporg\TranslationEvents\Urls;

?>
<!-- wp:table -->
<figure class="wp-block-table">
					<table class="event-stats">
						<thead>
							<tr>
                                <th><?php esc_html_e( 'Name', 'wporg-translate-events-2024' ); ?></th>
                                <th><?php esc_html_e( 'Remote', 'wporg-translate-events-2024' ); ?></th>
                                <th><?php esc_html_e( 'Host', 'wporg-translate-events-2024' ); ?></th>
                                <th><?php esc_html_e( 'Action', 'wporg-translate-events-2024' ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $attendees_not_contributing as $attendee ) : ?>
							<tr>
								<td>
									<!-- wp:wporg-translate-events-2024/attendee-avatar-name 
										<?php
										echo wp_json_encode(
											array(
												'user_id' => $attendee->user_id(),
												'is_new_contributor' => $attendee->is_new_contributor(),
											)
										);
										?>
									/-->
							</td>
							<td>
                                <?php if ( $attendee->is_remote() ) : ?>
                                    <span><?php esc_html_e( 'Yes', 'wporg-translate-events-2024' ); ?></span>
                                    <?php endif; ?>
                            </td>
                            <td>
						<?php if ( $attendee->is_host() ) : ?>
							<span><?php esc_html_e( 'Yes', 'wporg-translate-events-2024' ); ?></span>
							<?php endif; ?>
					</td>
					<td>
					<form class="add-remove-user-as-host" method="post" action="<?php echo esc_url( Urls::event_toggle_host( $event->id(), $attendee->user_id() ) ); ?>">
						<?php if ( $attendee->is_host() ) : ?>
							<input type="submit" class="button is-primary remove-as-host" value="<?php echo esc_attr__( 'Remove as host', 'wporg-translate-events-2024' ); ?>"/>
							<?php else : ?>
									<input type="submit" class="button is-secondary convert-to-host" value="<?php echo esc_attr__( 'Make co-host', 'wporg-translate-events-2024' ); ?>"/>
							<?php endif; ?>
							<?php if ( $event->is_hybrid() ) : ?>
								<a href="<?php echo esc_url( Urls::event_toggle_attendance_mode( $event->id(), $attendee->user_id() ) ); ?>" class="button set-attendance-mode"><?php $attendee->is_remote() ? esc_html_e( 'Set as on-site', 'wporg-translate-events-2024' ) : esc_html_e( 'Set as remote', 'wporg-translate-events-2024' ); ?></a>
							<?php endif; ?>
							<?php if ( ! $attendee->is_host() ) : ?>
								<a href="<?php echo esc_url( Urls::event_remove_attendee( $event->id(), $attendee->user_id() ) ); ?>" class="button remove-attendee"><?php esc_html_e( 'Remove', 'wporg-translate-events-2024' ); ?></a>
							<?php endif; ?>
						</form>
					</td>
							</tr>
							<?php endforeach; ?>
	
						</tbody>
					</table>
				</figure>
			<!-- /wp:table -->