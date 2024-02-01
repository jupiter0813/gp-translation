<?php
gp_title( __( 'Translation Events - Edit Event' ) );
gp_tmpl_header();

?>

<h2  class="event-page-title"><?php echo esc_html( $event_form_title ); ?></h2>
<form class="translation-event-form" action="" method="post">
	<?php wp_nonce_field( '_event_nonce', '_event_nonce' ); ?>
	<input type="hidden" name="action" value="submit_event_ajax">
	<input type="hidden" id="form-name" name="form_name" value="<?php echo esc_attr( $event_form_name ); ?>">
	<input type="hidden" id="event-id" name="event_id" value="<?php echo esc_attr( $event_id ); ?>">
	<div>
		<label for="event-title">Event Title:</label>
		<input type="text" id="event-title" name="event_title" value="<?php echo esc_html( $event_title ); ?>" required>
	</div>
	<div>
		<label for="event-description">Event Description:</label>
		<textarea id="event-description" name="event_description" rows="4" required><?php echo esc_html( $event_description ); ?></textarea>
	</div>
	<div>
		<label for="event-start">Start Date:</label>
		<input type="datetime-local" id="event-start" name="event_start" value="<?php echo esc_attr( $event_start ); ?>" required>
	</div>
	<div>
		<label for="event-end">End Date:</label>
		<input type="datetime-local" id="event-end" name="event_end" value="<?php echo esc_attr( $event_end ); ?>" required>
	</div>
    <div>
		<label for="event-timezone">Event Timezone:</label>
		<select id="event-timezone" name="event_timezone"  required>
			<?php echo wp_kses( wp_timezone_choice( $event_timezone, get_user_locale() ), array( 'optgroup' => array('label' => array()), 'option' => array('value' => array(), 'selected' => array() ) ) ); ?>
        </select>
	</div>
	<div>
		<label for="event-locale">Locale:</label>
		<input type="text" id="event-locale" name="event_locale" value="<?php echo esc_attr( $event_locale ); ?>">
	</div>
	<div>
		<label for="event-project-name">Project Name:</label>
		<input type="text" id="event-project-name" name="event_project_name" value="<?php echo esc_attr( $event_project_name ); ?>">
	</div>
	<button class="button is-primary" type="submit" id="edit-translation-event">Submit Event</button>
</form>
