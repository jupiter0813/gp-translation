<?php
/**
 * Events list page.
 */

namespace Wporg\TranslationEvents;

gp_title( __( 'Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
$event_page_title = __( 'Manage Attendees', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<?php
gp_tmpl_footer();
