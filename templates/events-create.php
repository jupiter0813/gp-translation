<?php
gp_title( __( 'Translation Events - Create new event' ) );
gp_tmpl_header();

?>

<h2  class="event-page-title">Create a new Translation Event</h2>

<form class="translation-event-form" action="" method="post">
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
		<label for="event-start-date">Start Date:</label>
		<input type="datetime-local" id="event-start_date" name="event_start_date" required>
	</div>
	<div>
		<label for="event-end-date">End Date:</label>
		<input type="datetime-local" id="event-end-date" name="event_end_date" required>
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
