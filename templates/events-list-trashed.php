<?php

namespace Wporg\TranslationEvents;

gp_title( __( 'Trashed Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
$event_page_title = __( 'Trashed Translation Events', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<div class="event-page-wrapper">
	<div class="event-left-col">
		<?php // TODO. ?>
	</div>
</div>

<div class="clear"></div>
<?php gp_tmpl_footer(); ?>
