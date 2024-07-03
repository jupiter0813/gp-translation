<?php
namespace Wporg\TranslationEvents\Theme_2024;

Renderer::header(
	array(
		'title'       => __( 'My Events', 'wporg-translate-events-2024' ),
		'breadcrumbs' => array(
			array(
				'title' => __( 'My Events', 'wporg-translate-events-2024' ),
				'url'   => null,
			),
		),
	)
);
?>

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'My Events', 'wporg-translate-events-2024' ); ?></h2>
<!-- /wp:heading -->

<?php Renderer::block( 'wporg-translate-events/events-pages-my-events', array( 'events' => $events ) ); ?>

<?php Renderer::footer(); ?>
