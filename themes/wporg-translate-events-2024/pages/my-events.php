<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Templates;
?>

<?php
Templates::header(
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
<?php Templates::footer(); ?>
