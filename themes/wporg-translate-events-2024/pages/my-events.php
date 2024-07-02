<?php
namespace Wporg\TranslationEvents\Theme_2024;

Renderer::header(
	array(
		'breadcrumbs' => array(
			array(
				'title' => __( 'My Events', 'wporg-translate-events-2024' ),
				'url'   => null,
			),
		),
	)
);
?>
<span>My Events</span>
<?php Renderer::footer(); ?>
