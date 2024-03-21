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
use WP_Query;
use Wporg\TranslationEvents\Event\Event_Form_Handler;

class Translation_Events {
	public const CPT = 'translation_event';

	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	public function __construct() {
		\add_action( 'wp_ajax_submit_event_ajax', array( $this, 'submit_event_ajax' ) );
		\add_action( 'wp_ajax_nopriv_submit_event_ajax', array( $this, 'submit_event_ajax' ) );
		\add_action( 'wp_enqueue_scripts', array( $this, 'register_translation_event_js' ) );
		\add_action( 'init', array( $this, 'register_event_post_type' ) );
		\add_action( 'add_meta_boxes', array( $this, 'event_meta_boxes' ) );
		\add_action( 'save_post', array( $this, 'save_event_meta_boxes' ) );
		\add_action( 'transition_post_status', array( $this, 'event_status_transition' ), 10, 3 );
		\add_filter( 'gp_nav_menu_items', array( $this, 'gp_event_nav_menu_items' ), 10, 2 );
		\add_filter( 'wp_insert_post_data', array( $this, 'generate_event_slug' ), 10, 2 );
		\add_action( 'gp_init', array( $this, 'gp_init' ) );
		\add_action( 'gp_before_translation_table', array( $this, 'add_active_events_current_user' ) );
		\register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	public function gp_init() {
		require_once __DIR__ . '/templates/helper-functions.php';
		require_once __DIR__ . '/includes/event-date.php';
		require_once __DIR__ . '/includes/routes/route.php';
		require_once __DIR__ . '/includes/routes/event/create.php';
		require_once __DIR__ . '/includes/routes/event/details.php';
		require_once __DIR__ . '/includes/routes/event/edit.php';
		require_once __DIR__ . '/includes/routes/event/list.php';
		require_once __DIR__ . '/includes/routes/user/attend-event.php';
		require_once __DIR__ . '/includes/routes/user/my-events.php';
		require_once __DIR__ . '/includes/event/event.php';
		require_once __DIR__ . '/includes/event/event-repository-interface.php';
		require_once __DIR__ . '/includes/event/event-repository.php';
		require_once __DIR__ . '/includes/event/event-repository-cached.php';
		require_once __DIR__ . '/includes/event/event-form-handler.php';
		require_once __DIR__ . '/includes/active-events-cache.php';
		require_once __DIR__ . '/includes/attendee-repository.php';
		require_once __DIR__ . '/includes/stats-calculator.php';
		require_once __DIR__ . '/includes/stats-listener.php';

		GP::$router->add( '/events?', array( 'Wporg\TranslationEvents\Routes\Event\List_Route', 'handle' ) );
		GP::$router->add( '/events/new', array( 'Wporg\TranslationEvents\Routes\Event\Create_Route', 'handle' ) );
		GP::$router->add( '/events/edit/(\d+)', array( 'Wporg\TranslationEvents\Routes\Event\Edit_Route', 'handle' ) );
		GP::$router->add( '/events/attend/(\d+)', array( 'Wporg\TranslationEvents\Routes\User\Attend_Event_Route', 'handle' ), 'post' );
		GP::$router->add( '/events/my-events', array( 'Wporg\TranslationEvents\Routes\User\My_Events_Route', 'handle' ) );
		GP::$router->add( '/events/([a-z0-9_-]+)', array( 'Wporg\TranslationEvents\Routes\Event\Details_Route', 'handle' ) );

		$active_events_cache = new Active_Events_Cache();
		$attendee_repository = new Attendee_Repository();
		$stats_listener      = new Stats_Listener( $active_events_cache, $attendee_repository );
		$stats_listener->start();
	}

	public function activate() {
		global $gp_table_prefix;
		$create_table = "
		CREATE TABLE `{$gp_table_prefix}event_actions` (
			`translate_event_actions_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`event_id` int(10) NOT NULL COMMENT 'Post_ID of the translation_event post in the wp_posts table',
			`original_id` int(10) NOT NULL COMMENT 'ID of the translation',
			`user_id` int(10) NOT NULL COMMENT 'ID of the user who made the action',
			`action` enum('approve','create','reject','request_changes') NOT NULL COMMENT 'The action that the user made (create, reject, etc)',
			`locale` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Locale of the translation',
			`happened_at` datetime NOT NULL COMMENT 'When the action happened, in UTC',
		PRIMARY KEY (`translate_event_actions_id`),
		UNIQUE KEY `event_per_translated_original_per_user` (`event_id`,`locale`,`original_id`,`user_id`)
		) COMMENT='Tracks translation actions that happened during a translation event'";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $create_table );
	}

	/**
	 * Register the event post type.
	 */
	public function register_event_post_type() {
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
			'rewrite'     => array( 'slug' => 'events' ),
			'show_ui'     => false,
		);

		register_post_type( self::CPT, $args );
	}
	/**
	 * Add meta boxes for the event post type.
	 */
	public function event_meta_boxes() {
		\add_meta_box( 'event_dates', 'Event Dates', array( $this, 'event_dates_meta_box' ), self::CPT, 'normal', 'high' );
	}

	/**
	 * Output the event dates meta box.
	 *
	 * @param  WP_Post $post The current post object.
	 */
	public function event_dates_meta_box( WP_Post $post ) {
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
	public function save_event_meta_boxes( int $post_id ) {
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
	 * Handle the event form submission for the creation, editing, and deletion of events. This function is called via AJAX.
	 */
	public function submit_event_ajax() {
		$form_handler = new Event_Form_Handler();
		// Nonce verification is done by the form handler.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$form_handler->handle( $_POST );
	}

	public function register_translation_event_js() {
		wp_register_style( 'translation-events-css', plugins_url( 'assets/css/translation-events.css', __FILE__ ), array(), filemtime( __DIR__ . '/assets/css/translation-events.css' ) );
		gp_enqueue_style( 'translation-events-css' );
		wp_register_script( 'translation-events-js', plugins_url( 'assets/js/translation-events.js', __FILE__ ), array( 'jquery', 'gp-common' ), filemtime( __DIR__ . '/assets/js/translation-events.js' ), false );
		gp_enqueue_script( 'translation-events-js' );
		wp_localize_script(
			'translation-events-js',
			'$translation_event',
			array(
				'url'          => admin_url( 'admin-ajax.php' ),
				'_event_nonce' => wp_create_nonce( self::CPT ),
			)
		);
	}

	/**
	 * Handle the event status transition.
	 *
	 * The user who creates the event will assist to it when it's published.
	 *
	 * @param string  $new_status The new post status.
	 * @param string  $old_status The old post status.
	 * @param WP_Post $post       The post object.
	 *
	 * @throws Exception
	 */
	public function event_status_transition( string $new_status, string $old_status, WP_Post $post ): void {
		if ( self::CPT !== $post->post_type ) {
			return;
		}
		if ( 'publish' === $new_status && ( 'new' === $old_status || 'draft' === $old_status ) ) {
			$attendee_repository = new Attendee_Repository();
			$current_user_id     = get_current_user_id();
			if ( ! $attendee_repository->is_attending( $post->ID, $current_user_id ) ) {
				$attendee_repository->add_attendee( $post->ID, $current_user_id );
			}
		}
	}

	/**
	 * Add the events link to the GlotPress main menu.
	 *
	 * @param array  $items    The menu items.
	 * @param string $location The menu location.
	 * @return array The modified menu items.
	 */
	public function gp_event_nav_menu_items( array $items, string $location ): array {
		if ( 'main' !== $location ) {
			return $items;
		}
		$new[ esc_url( gp_url( '/events/' ) ) ] = esc_html__( 'Events', 'gp-translation-events' );
		return array_merge( $items, $new );
	}

	/**
	 * Generate a slug for the event post type when we save a draft event or when we publish an event.
	 *
	 * Generate a slug based on the event title if:
	 * - The event is a draft.
	 * - The event is published and it was a draft just before.
	 *
	 * @param array $data    An array of slashed post data.
	 * @param array $postarr An array of sanitized, but otherwise unmodified post data.
	 * @return array The modified post data.
	 */
	public function generate_event_slug( array $data, array $postarr ): array {
		if ( self::CPT === $data['post_type'] ) {
			if ( 'draft' === $data['post_status'] ) {
				$data['post_name'] = sanitize_title( $data['post_title'] );
			}
			if ( 'publish' === $data['post_status'] ) {
				if ( is_numeric( $postarr['ID'] ) && 0 !== $postarr['ID'] ) {
					$post = get_post( $postarr['ID'] );
					if ( $post instanceof WP_Post ) {
						if ( 'draft' === $post->post_status ) {
							$data['post_name'] = sanitize_title( $data['post_title'] );
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Add the active events for the current user before the translation table.
	 *
	 * @throws Exception
	 */
	public function add_active_events_current_user(): void {
		$attendee_repository      = new Attendee_Repository();
		$user_attending_event_ids = $attendee_repository->get_events_for_user( get_current_user_id() );
		if ( empty( $user_attending_event_ids ) ) {
			return;
		}

		$current_datetime_utc       = ( new DateTime( 'now', new DateTimeZone( 'UTC' ) ) )->format( 'Y-m-d H:i:s' );
		$user_attending_events_args = array(
			'post_type'   => self::CPT,
			'post__in'    => $user_attending_event_ids,
			'post_status' => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query'  => array(
				array(
					'key'     => '_event_start',
					'value'   => $current_datetime_utc,
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
				array(
					'key'     => '_event_end',
					'value'   => $current_datetime_utc,
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'    => '_event_start',
			'orderby'     => 'meta_value',
			'order'       => 'ASC',
		);

		$user_attending_events_query = new WP_Query( $user_attending_events_args );
		$number_of_events            = $user_attending_events_query->post_count;
		if ( 0 === $number_of_events ) {
			return;
		}

		$content = '<div id="active-events-before-translation-table" class="active-events-before-translation-table">';
		/* translators: %d: Number of events */
		$content .= sprintf( _n( 'Contributing to %d event:', 'Contributing to %d events:', $number_of_events, 'gp-translation-events' ), $number_of_events );
		$content .= '&nbsp;&nbsp;';
		if ( $number_of_events > 3 ) {
			$counter = 0;
			while ( $user_attending_events_query->have_posts() && $counter < 2 ) {
				$user_attending_events_query->the_post();
				$url      = esc_url( gp_url( '/events/' . get_post_field( 'post_name', get_post() ) ) );
				$content .= '<span class="active-events-before-translation-table"><a href="' . $url . '" target="_blank">' . get_the_title() . '</a></span>';
				++$counter;
			}

			$remaining_events = $number_of_events - 2;
			$url              = esc_url( gp_url( '/events/' ) );
			/* translators: %d: Number of remaining events */
			$content .= '<span class="remaining-events"><a href="' . $url . '" target="_blank">' . sprintf( esc_html__( ' and %d more events.', 'gp-translation-events' ), $remaining_events ) . '</a></span>';

		} else {
			while ( $user_attending_events_query->have_posts() ) {
				$user_attending_events_query->the_post();
				$url      = esc_url( gp_url( '/events/' . get_post_field( 'post_name', get_post() ) ) );
				$content .= '<span class="active-events-before-translation-table"><a href="' . $url . '" target="_blank">' . get_the_title() . '</a></span>';
			}
		}
		$content .= '</div>';

		echo wp_kses(
			$content,
			array(
				'div'  => array(
					'id'    => array(),
					'class' => array(),
				),
				'span' => array(
					'class' => array(),
				),
				'a'    => array(
					'href'   => array(),
					'target' => array(),
				),
			)
		);
	}
}
Translation_Events::get_instance();
