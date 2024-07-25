<?php
namespace Wporg\TranslationEvents\Theme_2024;

$event_ids = $attributes['event_ids'] ?? array();

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();

global $translation_events;
?>
<!-- wp:pattern {"slug":"wporg-translate-events-2024/events-list"} /-->
