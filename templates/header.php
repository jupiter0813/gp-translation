<?php

use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Event\Event;

/** @var string $page_title */
/** @var string[] $breadcrumbs */

/** @var string $event_page_title */
/** @var Event $event */
/** @var Attendee[] $hosts */

gp_title( $page_title );
gp_breadcrumb_translation_events( $breadcrumbs );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
