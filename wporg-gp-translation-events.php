<?php
/**
 * Plugin Name: Translation Events
 * Plugin URI: https://github.com/Automattic/wporg-gp-translation-events
 * Description: A WordPress plugin for creating translation events.
 * Version: 1.0
 * Author: Automattic
 * Author URI: http://automattic.com/
 * Text Domain: gp-translation-events
 * License: GPLv2 or later
 *
 * @package Translation Events
 */


/**
 * Check if a slug is being used by another post type.
 *
 * @param string $slug The slug to check.
 * @return bool
 */
function slug_exists( $slug ) {
	$post_types = get_post_types( array( '_builtin' => false ) );
	foreach ( $post_types as $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( is_array( $post_type_object->rewrite ) && isset( $post_type_object->rewrite['slug'] ) ) {
			return ( $post_type_object->rewrite['slug'] === $slug );
		}
	}
	return false;
}

/**
 * Register the event post type.
 */
function register_event_post_type() {
	$slug = 'events';
	if ( slug_exists( $slug ) ) {
		$slug = 'translation-events';
	}
	$labels = array(
		'name'               => 'Translation Events',
		'singular_name'      => 'Translation Event',
		'menu_name'          => 'Translation Events',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Translation Event',
		'edit_item'          => 'Edit Translation Event',
		'new_item'           => 'New Translation Event',
		'view_item'          => 'View Translation Event',
		'search_items'       => 'Search Translation Events',
		'not_found'          => 'No translation events found',
		'not_found_in_trash' => 'No translation events found in trash',
	);

	$args = array(
		'labels'      => $labels,
		'public'      => true,
		'has_archive' => true,
		'menu_icon'   => 'dashicons-calendar',
		'supports'    => array( 'title', 'editor', 'thumbnail' ),
		'rewrite'     => array( 'slug' => $slug ),
	);

	register_post_type( 'event', $args );
}
/**
 * Add meta boxes for the event post type.
 */
function event_meta_boxes() {
	add_meta_box( 'event_dates', 'Event Dates', 'event_dates_meta_box', 'event', 'normal', 'high' );
	add_meta_box( 'event_locale', 'Event Locale', 'event_locale_meta_box', 'event', 'normal', 'high' );
	add_meta_box( 'event_project', 'Event Project', 'event_project_meta_box', 'event', 'normal', 'high' );
}

/**
 * Output the event dates meta box.
 *
 * @param  WP_Post $post The current post object.
 */
function event_dates_meta_box( $post ) {
	wp_nonce_field( 'event_dates_nonce', 'event_dates_nonce' );
	$start_date = get_post_meta( $post->ID, '_event_start_date', true );
	$end_date   = get_post_meta( $post->ID, '_event_end_date', true );
	echo '<label for="event_start_date">Start Date: </label>';
	echo '<input type="date" id="event_start_date" name="event_start_date" value="' . esc_attr( $start_date ) . '" required>';
	echo '<label for="event_end_date">End Date: </label>';
	echo '<input type="date" id="event_end_date" name="event_end_date" value="' . esc_attr( $end_date ) . '" required>';
}

/**
 * Output the event locale meta box.
 *
 * @param  WP_Post $post The current post object.
 */
function event_locale_meta_box( $post ) {
	wp_nonce_field( 'event_locale_nonce', 'event_locale_nonce' );
	$locale = get_post_meta( $post->ID, '_event_locale', true );
	echo '<label for="event_locale">Locale: </label>';
	echo '<input type="text" id="event_locale" name="event_locale" value="' . esc_attr( $locale ) . '">';
}

/**
 * Output the event project name meta box.
 *
 * @param  WP_Post $post The current post object.
 */
function event_project_meta_box( $post ) {
	wp_nonce_field( 'event_project_nonce', 'event_project_nonce' );
	$project_name = get_post_meta( $post->ID, '_event_project_name', true );
	echo '<label for="event_project_name">Project Name: </label>';
	echo '<input type="text" id="event_project_name" name="event_project_name" value="' . esc_attr( $project_name ) . '">';
}

/**
 * Save the event meta boxes.
 *
 * @param  int $post_id The current post ID.
 */
function save_event_meta_boxes( $post_id ) {
	$nonces = array( 'event_dates', 'event_locale', 'event_project' );
	foreach ( $nonces as $nonce ) {
		if ( ! isset( $_POST[ $nonce . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ $nonce . '_nonce' ], $nonce . '_nonce' ) ) {
			return;
		}
	}

	$fields = array( 'event_start_date', 'event_end_date', 'event_locale', 'event_project_name' );
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
		}
	}
}

function submit_event_ajax() {
	$event_id;
	if ( 'create_event' === $_POST['form_name'] ) {
		if ( ! isset( $_POST['create_event_nonce'] ) || ! wp_verify_nonce( $_POST['create_event_nonce'], 'create_event_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );
		}
	}
	if ( 'edit_event' === $_POST['form_name'] ) {
		if ( ! isset( $_POST['edit_event_nonce'] ) || ! wp_verify_nonce( $_POST['edit_event_nonce'], 'edit_event_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );
		}
	}

	$title        = sanitize_text_field( $_POST['event_title'] );
	$description  = sanitize_text_field( $_POST['event_description'] );
	$start_date   = sanitize_text_field( $_POST['event_start_date'] );
	$end_date     = sanitize_text_field( $_POST['event_end_date'] );
	$locale       = sanitize_text_field( $_POST['event_locale'] );
	$project_name = sanitize_text_field( $_POST['event_project_name'] );

	if ( 'create_event' === $_POST['form_name'] ) {
		$event_id = wp_insert_post(
			array(
				'post_type'    => 'event',
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => 'publish',
			)
		);
	}
	if ( 'edit_event' === $_POST['form_name'] ) {
		$event_id = $_POST['event_id'];
		$event    = get_post( $event_id );
		if ( ! $event || 'event' !== $event->post_type || ! current_user_can( 'edit_post', $event_id ) ) {
			wp_send_json_error( 'Event does not exist' );
		}
		wp_update_post(
			array(
				'ID'           => $event_id,
				'post_title'   => $title,
				'post_content' => $description,
			)
		);
	}
	update_post_meta( $event_id, '_event_start_date', $start_date );
	update_post_meta( $event_id, '_event_end_date', $end_date );
	if ( $locale ) {
		update_post_meta( $event_id, '_event_locale', $locale );
	}
	if ( $project_name ) {
		update_post_meta( $event_id, '_event_project_name', $project_name );
	}

	wp_send_json_success( 'success' );
}

add_action( 'wp_ajax_submit_event_ajax', 'submit_event_ajax' );
add_action( 'wp_ajax_nopriv_submit_event_ajax', 'submit_event_ajax' );


function register_translation_event_js() {
	wp_register_style( 'translation-events-css', plugins_url( 'assets/css/translation-events.css', __FILE__ ), array(), filemtime( __DIR__ . '/assets/css/translation-events.css' ) );
	gp_enqueue_style( 'translation-events-css' );
	wp_register_script( 'translation-events-js', plugins_url( 'assets/js/translation-events.js', __FILE__ ), array( 'jquery', 'gp-common' ), filemtime( __DIR__ . '/assets/js/translation-events.js' ), false );
	gp_enqueue_script( 'translation-events-js' );
	wp_localize_script(
		'translation-events-js',
		'$translation_event',
		array(
			'url'          => admin_url( 'admin-ajax.php' ),
			'create_nonce' => wp_create_nonce( 'create_translation_event' ),
			'edit_nonce'   => wp_create_nonce( 'edit_translation_event' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'register_translation_event_js' );
add_action( 'init', 'register_event_post_type' );
add_action( 'add_meta_boxes', 'event_meta_boxes' );
add_action( 'save_post', 'save_event_meta_boxes' );

add_action(
	'gp_init',
	function() {
		require_once __DIR__ . '/includes/class-wporg-gp-translation-events-route.php';
		GP::$router->prepend( '/events?', array( 'WPORG_GP_Translation_Events_Route', 'events_list' ), 'get' );
		GP::$router->prepend( '/events/new', array( 'WPORG_GP_Translation_Events_Route', 'events_create' ), 'get' );
		GP::$router->prepend( '/events/edit/(\d+)', array( 'WPORG_GP_Translation_Events_Route', 'events_edit' ), 'get' );
		GP::$router->prepend( '/events/([a-z0-9_-]+)', array( 'WPORG_GP_Translation_Events_Route', 'events_details' ), 'get' );

		require_once __DIR__ . '/includes/class-wporg-gp-translation-events-translation-listener.php';
		$wporg_gp_translation_events_listener = new WPORG_GP_Translation_Events_Translation_Listener();
		$wporg_gp_translation_events_listener->start();
	}
);
