<?php
gp_title( __( 'Translation Events - Edit Event' ) );
gp_tmpl_header();

?>

<h2>Edit Translation Event</h2>
<form action="" method="post">
	<?php wp_nonce_field( 'edit_event', 'edit_event' ); ?>
	<input type="hidden" name="action" value="submit_event">
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
		<input type="text" id="event_locale" name="event_locale" required>
	</div>
	<div>
		<label for="event_project_name">Project Name:</label>
		<input type="text" id="event_project_name" name="event_project_name" required>
	</div>
	<button type="submit">Submit Event</button>
</form>
