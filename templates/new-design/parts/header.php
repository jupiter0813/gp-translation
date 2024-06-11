<?php
namespace Wporg\TranslationEvents\Templates\NewDesign\Parts;

use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var string $html_title */
/** @var string|callable $page_title */
/** @var string $url */
/** @var string $image_url */
/** @var string $html_description */
/** @var ?callable $sub_head */
/** @var ?string[] $breadcrumbs */

$html_title       = $html_title ?? esc_html__( 'Translation Events', 'gp-translation-events' );
$url              = $url ?? Urls::events_home();
$html_description = $html_description ?? esc_html__( 'WordPress Translation Events', 'gp-translation-events' );
$image_url        = $image_url ?? Urls::event_default_image();

gp_title( $html_title );
Templates::part( 'site-header', get_defined_vars() );
Templates::block( 'wporg/site-breadcrumbs' );
