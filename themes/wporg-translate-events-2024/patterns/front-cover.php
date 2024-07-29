<?php
namespace Wporg\TranslationEvents\Theme_2024;

ob_start();
?>
<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Get involved</h4>
<!-- /wp:heading -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"100%","fontSize":"medium","fontFamily":"eb-garamond"} -->
<div class="wp-block-column has-eb-garamond-font-family has-medium-font-size" style="flex-basis:100%"><!-- wp:group {"style":{"border":{"radius":"2px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30"}}},"borderColor":"light-grey-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-light-grey-1-border-color" style="border-width:1px;border-radius:2px;padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-0"}}}},"textColor":"charcoal-0","fontSize":"heading-6"} -->
<a href="/events/">Host an event</a>
<p class="has-charcoal-0-color has-text-color has-link-color has-heading-6-font-size">Create your own translation event.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->
		
<!-- wp:column {"width":"100%","fontSize":"medium","fontFamily":"eb-garamond"} -->
<div class="wp-block-column has-eb-garamond-font-family has-medium-font-size" style="flex-basis:100%"><!-- wp:group {"style":{"border":{"radius":"2px","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30"}}},"borderColor":"light-grey-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-light-grey-1-border-color" style="border-width:1px;border-radius:2px;padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"style":{"layout":{"selfStretch":"fit","flexSize":null},"elements":{"link":{"color":{"text":"var:preset|color|charcoal-0"}}}},"textColor":"charcoal-0","fontSize":"heading-6"} -->
<a href="/events/">My events</a>
<p class="has-charcoal-0-color has-text-color has-link-color has-heading-6-font-size">Manage events you’ve created, or pledged to attend.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>

<?php
$content = ob_get_clean();

register_block_pattern(
	'wporg-translate-events-2024/front-cover',
	array(
		'title'      => __( 'Events Home Cover', 'wporg-translate-events-2024' ),
		'categories' => array( 'featured' ),
		'content'    => $content,
	)
);
