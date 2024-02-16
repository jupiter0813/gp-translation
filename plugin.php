<?php
/**
 * Plugin Name: Translation Events
 * Plugin URI: https://github.com/WordPress/wporg-gp-translation-events/
 * Description: A WordPress plugin for creating translation events.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Author: WordPress Contributors
 * Author URI: https://github.com/WordPress/wporg-gp-translation-events/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: gp-translation-events
 *
 * @package Translation Events
 */

namespace Wporg\TranslationEvents;

use DateTime;
use DateTimeZone;
use Exception;
use GP;
use WP_Post;

/**
 * Check if a slug is being used by another post type.
 *
 * @param string $slug The slug to check.
 *
 * @return bool
 */
function slug_exists( string $slug ): bool {
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
		echo 'The slug "' . esc_html( $slug ) . '" is already in use by another post type.';
		return;
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
		'supports'    => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'rewrite'     => array( 'slug' => $slug ),
		'show_ui'     => false,
	);

	register_post_type( 'event', $args );
}
/**
 * Add meta boxes for the event post type.
 */
function event_meta_boxes() {
	add_meta_box( 'event_dates', 'Event Dates', 'Wporg\TranslationEvents\event_dates_meta_box', 'event', 'normal', 'high' );
}

/**
 * Output the event dates meta box.
 *
 * @param  WP_Post $post The current post object.
 */
function event_dates_meta_box( WP_Post $post ) {
	wp_nonce_field( 'event_dates_nonce', 'event_dates_nonce' );
	$event_start = get_post_meta( $post->ID, '_event_start', true );
	$event_end   = get_post_meta( $post->ID, '_event_end', true );
	echo '<label for="event_start">Start Date: </label>';
	echo '<input type="date" id="event_start" name="event_start" value="' . esc_attr( $event_start ) . '" required>';
	echo '<label for="event_end">End Date: </label>';
	echo '<input type="date" id="event_end" name="event_end" value="' . esc_attr( $event_end ) . '" required>';
}

/**
 * Save the event meta boxes.
 *
 * @param  int $post_id The current post ID.
 */
function save_event_meta_boxes( int $post_id ) {
	$nonces = array( 'event_dates' );
	foreach ( $nonces as $nonce ) {
		$nonce_name = $nonce . '_nonce';
		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return;
		}
		$nonce_value = sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) );
		if ( ! wp_verify_nonce( $nonce_value, $nonce_name ) ) {
			return;
		}
	}

	$fields = array( 'event_start', 'event_end' );
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}

/**
 * Validate the event dates.
 *
 * @param string $event_start The event start date.
 * @param string $event_end The event end date.
 * @return bool Whether the event dates are valid.
 * @throws Exception When dates are invalid.
 */
function validate_event_dates( string $event_start, string $event_end ): bool {
	if ( ! $event_start || ! $event_end ) {
		return false;
	}
	$event_start = new DateTime( $event_start );
	$event_end   = new DateTime( $event_end );
	if ( $event_start < $event_end ) {
		return true;
	}
	return false;
}

function submit_event_ajax() {
	$event_id         = null;
	$response_message = '';
	$form_actions     = array( 'draft', 'publish', 'delete' );

	$is_nonce_valid = false;
	$nonce_name     = '_event_nonce';
	if ( isset( $_POST[ $nonce_name ] ) ) {
		$nonce_value = sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) );
		if ( wp_verify_nonce( $nonce_value, $nonce_name ) ) {
			$is_nonce_valid = true;
		}
	}
	if ( ! $is_nonce_valid ) {
		wp_send_json_error( 'Nonce verification failed' );
	}

	$title          = isset( $_POST['event_title'] ) ? sanitize_text_field( wp_unslash( $_POST['event_title'] ) ) : '';
	$description    = isset( $_POST['event_description'] ) ? sanitize_text_field( wp_unslash( $_POST['event_description'] ) ) : '';
	$event_start    = isset( $_POST['event_start'] ) ? sanitize_text_field( wp_unslash( $_POST['event_start'] ) ) : '';
	$event_end      = isset( $_POST['event_end'] ) ? sanitize_text_field( wp_unslash( $_POST['event_end'] ) ) : '';
	$event_timezone = isset( $_POST['event_timezone'] ) ? sanitize_text_field( wp_unslash( $_POST['event_timezone'] ) ) : '';

	$is_valid_event_date = false;
	try {
		$is_valid_event_date = validate_event_dates( $event_start, $event_end );
	} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		// Deliberately ignored, handled below.
	}
	if ( ! $is_valid_event_date ) {
		wp_send_json_error( 'Invalid event dates' );
	}

	$event_status = '';
	if ( isset( $_POST['event_form_action'] ) && in_array( $_POST['event_form_action'], $form_actions, true ) ) {
		$event_status = sanitize_text_field( wp_unslash( $_POST['event_form_action'] ) );
	}

	if ( ! isset( $_POST['form_name'] ) ) {
		wp_send_json_error( 'Form name must be set' );
	}
	$action = sanitize_text_field( wp_unslash( $_POST['form_name'] ) );
	if ( ! in_array( $action, array( 'create_event', 'edit_event', 'delete_event' ), true ) ) {
		wp_send_json_error( 'Invalid form name' );
	}

	if ( 'create_event' === $action ) {
		$event_id         = wp_insert_post(
			array(
				'post_type'    => 'event',
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => $event_status,
			)
		);
		$response_message = 'Event created successfully!';
	}
	if ( 'edit_event' === $action ) {
		if ( ! isset( $_POST['event_id'] ) ) {
			wp_send_json_error( 'Event id is required' );
		}
		$event_id = sanitize_text_field( wp_unslash( $_POST['event_id'] ) );
		$event    = get_post( $event_id );
		if ( ! $event || 'event' !== $event->post_type || ! ( current_user_can( 'edit_post', $event->ID ) || intval( $event->post_author ) === get_current_user_id() ) ) {
			wp_send_json_error( 'Event does not exist' );
		}
		wp_update_post(
			array(
				'ID'           => $event_id,
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => $event_status,
			)
		);
		$response_message = 'Event updated successfully!';
	}
	if ( 'delete_event' === $action ) {
		$event_id = sanitize_text_field( wp_unslash( $_POST['event_id'] ) );
		$event    = get_post( $event_id );
		if ( ! $event || 'event' !== $event->post_type ) {
			wp_send_json_error( 'Event does not exist' );
		}
		if ( ! ( current_user_can( 'delete_post', $event->ID ) || get_current_user_id() === $event->post_author ) ) {
			wp_send_json_error( 'You do not have permission to delete this event' );
		}
		$stats_calculator = new WPORG_GP_Translation_Events_Stats_Calculator();
		try {
			$event_stats = $stats_calculator->for_event( $event );
		} catch ( Exception $e ) {
			wp_send_json_error( 'Failed to calculate event stats' );
		}
		if ( ! empty( $event_stats->rows() ) ) {
			wp_send_json_error( 'Event has translations and cannot be deleted' );
		}
		wp_trash_post( $event_id );
		$response_message = 'Event deleted successfully!';
	}
	if ( ! $event_id ) {
		wp_send_json_error( 'Event could not be created or updated' );
	}
	if ( 'delete_event' !== $_POST['form_name'] ) {
		try {
			update_post_meta( $event_id, '_event_start', convert_to_utc( $event_start, $event_timezone ) );
			update_post_meta( $event_id, '_event_end', convert_to_utc( $event_end, $event_timezone ) );
		} catch ( Exception $e ) {
			wp_send_json_error( 'Invalid start or end' );
		}

		update_post_meta( $event_id, '_event_timezone', $event_timezone );
	}
	try {
		Active_Events_Cache::invalidate();
	} catch ( Exception $e ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $e );
	}

	list( $permalink, $post_name ) = get_sample_permalink( $event_id );
	$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
	wp_send_json_success(
		array(
			'message'        => $response_message,
			'eventId'        => $event_id,
			'eventUrl'       => str_replace( '%pagename%', $post_name, $permalink ),
			'eventStatus'    => $event_status,
			'eventEditUrl'   => esc_url( gp_url( '/events/edit/' . $event_id ) ),
			'eventDeleteUrl' => esc_url( gp_url( '/events/my-events/' ) ),
		)
	);
}

add_action( 'wp_ajax_submit_event_ajax', 'Wporg\TranslationEvents\submit_event_ajax' );
add_action( 'wp_ajax_nopriv_submit_event_ajax', 'Wporg\TranslationEvents\submit_event_ajax' );

/**
 * Convert a date time in a time zone to UTC.
 *
 * @param string $date_time The date time in the time zone.
 * @param string $time_zone The time zone.
 * @return string The date time in UTC.
 * @throws Exception When dates are invalid.
 */
function convert_to_utc( string $date_time, string $time_zone ): string {
	$date_time = new DateTime( $date_time, new DateTimeZone( $time_zone ) );
	$date_time->setTimezone( new DateTimeZone( 'UTC' ) );
	return $date_time->format( 'Y-m-d H:i:s' );
}

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
			'_event_nonce' => wp_create_nonce( 'translation_event' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'Wporg\TranslationEvents\register_translation_event_js' );
add_action( 'init', 'Wporg\TranslationEvents\register_event_post_type' );
add_action( 'add_meta_boxes', 'Wporg\TranslationEvents\event_meta_boxes' );
add_action( 'save_post', 'Wporg\TranslationEvents\save_event_meta_boxes' );

/**
 * Add the events link to the GlotPress main menu.
 *
 * @param array $items The menu items.
 * @return array The modified menu items.
 */
function gp_event_nav_menu_items( array $items ): array {
	$new[ esc_url( gp_url( '/events/' ) ) ] = esc_html__( 'Events', 'gp-translation-events' );
	return array_merge( $items, $new );
}
// Add the events link to the GlotPress main menu.
add_filter( 'gp_nav_menu_items', 'Wporg\TranslationEvents\gp_event_nav_menu_items' );

/**
 * Generate a slug for the event post type when we save a draft event.
 *
 * Generate a slug based on the event title if it's not provided.
 *
 * @param array $data    An array of slashed post data.
 * @return array The modified post data.
 */
function generate_event_slug( $data ) {
	if ( 'event' === $data['post_type'] && 'draft' === $data['post_status'] ) {
		if ( empty( $data['post_name'] ) ) {
			$data['post_name'] = sanitize_title( $data['post_title'] );
		}
	}

	return $data;
}

add_filter( 'wp_insert_post_data', 'Wporg\TranslationEvents\generate_event_slug', 10, 1 );

add_action(
	'gp_init',
	function () {
		require_once __DIR__ . '/includes/route.php';
		GP::$router->add( '/events?', array( 'Wporg\TranslationEvents\Route', 'events_list' ) );
		GP::$router->add( '/events/new', array( 'Wporg\TranslationEvents\Route', 'events_create' ) );
		GP::$router->add( '/events/edit/(\d+)', array( 'Wporg\TranslationEvents\Route', 'events_edit' ) );
		GP::$router->add( '/events/attend/(\d+)', array( 'Wporg\TranslationEvents\Route', 'events_attend' ), 'post' );
		GP::$router->add( '/events/my-events', array( 'Wporg\TranslationEvents\Route', 'events_user_created' ) );
		GP::$router->add( '/events/([a-z0-9_-]+)', array( 'Wporg\TranslationEvents\Route', 'events_details' ) );

		require_once __DIR__ . '/includes/event.php';
		require_once __DIR__ . '/includes/active-events-cache.php';
		require_once __DIR__ . '/includes/stats-calculator.php';
		require_once __DIR__ . '/includes/translation-listener.php';

		$active_events_cache                  = new Active_Events_Cache();
		$wporg_gp_translation_events_listener = new Translation_Listener( $active_events_cache );
		$wporg_gp_translation_events_listener->start();
	}
);
