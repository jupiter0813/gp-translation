<?php
gp_title( __( 'Translation Events - Create new event' ) );
gp_tmpl_header();

?>

<h2>Create a new Translation Event</h2>

<form id="event_submission_form" action="" method="post">
	<?php wp_nonce_field( 'create_event_nonce', 'create_event_nonce' ); ?>
	<input type="hidden" name="action" value="submit_event_ajax">
	<div>
		<label for="event_title">Event Title:</label>
		<input type="text" id="event_title" name="event_title" required>
	</div>
	<div>
		<label for="event_description">Event Description:</label>
		<textarea id="event_description" name="event_description" rows="4" required></textarea>
	</div>
	<div>
		<label for="event_start_date">Start Date:</label>
		<input type="date" id="event_start_date" name="event_start_date" required>
	</div>
	<div>
		<label for="event_end_date">End Date:</label>
		<input type="date" id="event_end_date" name="event_end_date" required>
	</div>
	<div>
		<label for="event_locale">Locale:</label>
		<input type="text" id="event_locale" name="event_locale">
	</div>
	<div>
		<label for="event_project_name">Project Name:</label>
		<input type="text" id="event_project_name" name="event_project_name">
	</div>
	<button type="button" id="submit_event">Submit Event</button>
</form>
