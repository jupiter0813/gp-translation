<!-- wp:table -->
<figure class="wp-block-table">
					<table class="event-stats">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Attendee', 'wporg-translate-events-2024' ); ?></th>
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
								<td>Host</td>
								<td>Make host</td>
							</tr>
							<?php endforeach; ?>
							
						</tbody>
					</table>
				</figure>
			<!-- /wp:table -->