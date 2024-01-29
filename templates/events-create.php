<?php
gp_title( __( 'Translation Events - Create new event' ) );
gp_tmpl_header();

?>
<h2  class="event-page-title">Create a new Translation Event</h2>

<form class="translation-event-form" id="create-event-form" action="" method="post">
	<?php wp_nonce_field( 'create_event_nonce', 'create_event_nonce' ); ?>
	<input type="hidden" name="action" value="submit_event_ajax">
	<input type="hidden" name="form_name" value="create_event">
	<div>
		<label for="event-title">Event Title:</label>
		<input type="text" id="event-title" name="event_title" required>
	</div>
	<div>
		<label for="event-description">Event Description:</label>
		<textarea id="event-description" name="event_description" rows="4" required></textarea>
	</div>
	<div>
		<label for="event-start">Start Date:</label>
		<input type="datetime-local" id="event-start" name="event_start" required>
	</div>
	<div>
		<label for="event-end">End Date:</label>
		<input type="datetime-local" id="event-end" name="event_end" required>
	</div>
	<div>
		<label for="event-timezone">Event Timezone:</label>
		<select id="event-timezone" name="event_timezone" required>
			<?php echo wp_kses( wp_timezone_choice( 'UTC', get_user_locale() ), array( 'optgroup' => array('label' => array()), 'option' => array('value' => array() ) ) ); ?>
    	</select>
	</div>
	<div>
		<label for="event-locale">Locale:</label>
		<input type="text" id="event-locale" name="event_locale">
	</div>
	<div>
		<label for="event-project-name">Project Name:</label>
		<input type="text" id="event-project-name" name="event_project_name">
	</div>
	<button class="button is-primary" type="button" id="submit-event">Submit Event</button>
</form>
