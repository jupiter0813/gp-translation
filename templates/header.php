<?php
/** @var string $event_page_title */

gp_title( __( 'Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
