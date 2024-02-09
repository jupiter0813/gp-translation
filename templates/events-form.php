<?php
gp_title( __( 'Translation Events - ' ) . esc_html( $event_form_title ) );
gp_tmpl_header();

?>

<h2  class="event-page-title"><?php echo esc_html( $event_form_title ); ?></h2>
<form class="translation-event-form" action="" method="post">
	<?php wp_nonce_field( '_event_nonce', '_event_nonce' ); ?>
	<input type="hidden" name="action" value="submit_event_ajax">
	<input type="hidden" id="form-name" name="form_name" value="<?php echo esc_attr( $event_form_name ); ?>">
	<input type="hidden" id="event-id" name="event_id" value="<?php echo esc_attr( $event_id ); ?>">
	<input type="hidden" id="event-form-action" name="event_form_action">
	<div>
		<label for="event-title">Event Title</label>
		<input type="text" id="event-title" name="event_title" value="<?php echo esc_html( $event_title ); ?>" required>
	</div>
	<p id="event-url" class="<?php echo esc_attr( $css_show_url ); ?>">
		<span>Event URL</span>
		 <a class="event-permalink" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_url( $permalink ); ?></a>
	</p>
	<div>
		<label for="event-description">Event Description</label>
		<textarea id="event-description" name="event_description" rows="4" required><?php echo esc_html( $event_description ); ?></textarea>
	</div>
	<div>
		<label for="event-start">Start Date</label>
		<input type="datetime-local" id="event-start" name="event_start" value="<?php echo esc_attr( $event_start ); ?>" required>
	</div>
	<div>
		<label for="event-end">End Date</label>
		<input type="datetime-local" id="event-end" name="event_end" value="<?php echo esc_attr( $event_end ); ?>" required>
	</div>
	<div>
		<label for="event-timezone">Event Timezone</label>
		<select id="event-timezone" name="event_timezone"  required>
			<?php
			echo wp_kses(
				wp_timezone_choice( $event_timezone, get_user_locale() ),
				array(
					'optgroup' => array( 'label' => array() ),
					'option'   => array(
						'value'    => array(),
						'selected' => array(),
					),
				)
			);
			?>
		</select>
	</div>
	<div>
		<label for="event-locale">Locale</label>
		<input type="text" id="event-locale" name="event_locale" value="<?php echo esc_attr( $event_locale ); ?>">
	</div>
	<div>
		<label for="event-project-name">Project Name</label>
		<input type="text" id="event-project-name" name="event_project_name" value="<?php echo esc_attr( $event_project_name ); ?>">
	</div>
	<div class="submit-btn-group">
	<?php if ( $event_id ) : ?>
		<?php if ( isset( $event_status ) && 'draft' === $event_status ) : ?>
			<button class="button is-primary save-draft submit-event" type="submit" data-event-status="draft">Update Draft</button>
		<?php endif; ?>
	<button class="button is-primary submit-event" type="submit"  data-event-status="publish">
		<?php echo ( isset( $event_status ) && 'publish' === $event_status ) ? esc_html( 'Update Event' ) : esc_html( 'Publish Event' ); ?>
	</button>
	<?php else : ?>
		<button class="button is-primary save-draft submit-event" type="submit" data-event-status="draft">Save Draft</button>
		<button class="button is-primary submit-event" type="submit"  data-event-status="publish">Publish Event</button>
	<?php endif; ?>
	</div>
</form>
