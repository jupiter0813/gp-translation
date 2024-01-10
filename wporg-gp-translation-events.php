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

add_action( 'init', 'register_event_post_type' );
