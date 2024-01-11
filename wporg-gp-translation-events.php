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

require_once __DIR__ . '/includes/class-wporg-gp-translation-events-route.php';

function register_routes() {
	GP::$router->prepend( '/events?', array( 'WPORG_GP_Translation_Events_Route', 'events_list' ), 'get' );
}
/**
 * Register the event post type.
 */
function register_event_post_type() {
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

add_action( 'init', 'register_event_post_type' );
add_action( 'add_meta_boxes', 'event_meta_boxes' );
add_action( 'save_post', 'save_event_meta_boxes' );

register_routes();
