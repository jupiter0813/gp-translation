<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Event\Events_Query_Result;

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();
?>

<?php render_header( __( 'My Events', 'wporg-translate-events-2024' ) ); ?>

<span>my-events</span>

<!-- wp:wporg-translate-events-2024/footer /-->
