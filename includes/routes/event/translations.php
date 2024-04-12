<?php

namespace Wporg\TranslationEvents\Routes\Event;

use Exception;
use GP;
use Wporg\TranslationEvents\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Stats_Calculator;
use Wporg\TranslationEvents\Translation_Events;

/**
 * Displays the event details page.
 */
class Translations_Route extends Route {

	public function __construct() {
		parent::__construct();
//		$this->event_repository    = Translation_Events::get_event_repository();
//		$this->attendee_repository = Translation_Events::get_attendee_repository();
	}

	public function handle( int $event_id, string $locale ): void {
		global $wpdb, $gp_table_prefix;

		// Get the different projects for the event and locale
		$project_ids = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT DISTINCT p.id
				FROM {$gp_table_prefix}event_actions ea
				JOIN {$gp_table_prefix}originals o ON ea.original_id = o.id
				JOIN {$gp_table_prefix}projects p ON o.project_id = p.id
				WHERE ea.event_id = %d
				AND ea.locale = %s
				",
				$event_id,
				$locale
			)
		);

		// Get the translations for the current project_id, event_id and locale
		foreach ( $project_ids as $project_id ) {
			$translations = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT DISTINCT t.*
					FROM {$gp_table_prefix}event_actions ea
					JOIN {$gp_table_prefix}originals o ON ea.original_id = o.id
					JOIN {$gp_table_prefix}translation_sets ts ON o.project_id = ts.project_id AND ea.locale = ts.locale
					JOIN {$gp_table_prefix}translations t ON ts.id = t.translation_set_id
					WHERE ea.event_id = %d AND ea.locale = %s
					AND o.project_id = %d
					",
					$event_id,
					$locale,
					$project_id->id
				),
				ARRAY_A
			);
//			dd($translations);
			$locale_slug = $locale;
			$slug = null;
			$project = GP::$project->get( $project_id );
			$translation_set = GP::$translation_set->get( $translations[0][ 'translation_set_id' ] );
			$can_approve = GP::$permission->current_user_can( 'approve', $project );
			$can_write = GP::$permission->current_user_can( 'write', $project );
			$url = gp_url( '/projects/' . $project->slug . '/' . $locale_slug . '/' . $slug );
			$discard_warning_url = gp_url( '/projects/' . $project->slug . '/' . $locale_slug . '/' . $slug . '/discard-warning' );
			$set_priority_url = gp_url( '/projects/' . $project->slug . '/' . $locale_slug . '/' . $slug . '/set-priority' );
			$set_status_url = gp_url( '/projects/' . $project->slug . '/' . $locale_slug . '/' . $slug . '/set-status' );
			$word_count_type = 'original';
			$filters = array();
			$sort = array();
			$glossary = GP::$glossary->get( $project->id, $locale );
			$page = 1;
			$per_page = 10000;
			$total_translations_count = 0;
			$text_direction = 'ltr';
			foreach ( $translations as $translation ) {
//				$args['trasnslations'][] = GP::$translation->get( $translation->id );
				$args['translations'][] = new \Translation_Entry( $translation );
			}


		//dd($event_id, $translations, get_defined_vars() );
		$this->tmpl('translations', get_defined_vars());
		}

		// Get the translation_set ids for the event and locale
//		$translations = $wpdb->get_results(
//			$wpdb->prepare(
//				"
//				SELECT DISTINCT t.*
//				FROM {$gp_table_prefix}event_actions ea
//				JOIN {$gp_table_prefix}originals o ON ea.original_id = o.id
//				JOIN {$gp_table_prefix}translation_sets ts ON o.project_id = ts.project_id AND ea.locale = ts.locale
//				JOIN {$gp_table_prefix}translations t ON ts.id = t.translation_set_id
//				WHERE ea.event_id = %d AND ea.locale = %s
//				",
//				$event_id,
//				$locale
//			)
//		);


		$translation_set_ids = $wpdb->get_results(
			$wpdb->prepare(
				"
				select translation_set_id
				from {$gp_table_prefix}translations
				where event_id = %d
				and locale = %s
			",
				array(
					$event_id,
					$locale
				)
			)
		);

		// Get the original_id of the translations for the event and locale
		$originals = $wpdb->get_results(
			$wpdb->prepare(
				"
				select original_id
				from {$gp_table_prefix}event_actions
				where event_id = %d
				and locale = %s
			",
				array(
					$event_id,
					$locale
				)
			)
		);

		$translation_sets = $wpdb->get_results(
			$wpdb->prepare(
				"
				select translation_set_id
				from {$gp_table_prefix}translations
				where original_id = %d
			",
				array(
					$originals[0]->original_id
				)
			)
		);
		dd($event_id, $locale, $originals);
	}
}
