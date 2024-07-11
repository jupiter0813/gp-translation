<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Event\Events_Query_Result;

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();

$page_title = __( 'My Events', 'wporg-translate-events-2024' );
?>

<!-- wp:wporg-translate-events-2024/header <?php echo wp_json_encode( array( 'title' => $page_title ) ); ?> /-->

<span>my-events</span>

<!-- wp:wporg-translate-events-2024/footer /-->
