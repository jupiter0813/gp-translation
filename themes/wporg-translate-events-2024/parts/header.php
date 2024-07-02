<?php
namespace Wporg\TranslationEvents\Templates\NewDesign\Parts;

use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var string $url */
/** @var string $html_title */
/** @var string $html_description */
/** @var string $image_url */

$html_title       = $html_title ?? esc_html__( 'Translation Events', 'gp-translation-events' );
$url              = $url ?? Urls::events_home();
$html_description = $html_description ?? esc_html__( 'WordPress Translation Events', 'gp-translation-events' );
$image_url        = $image_url ?? Urls::event_default_image();

add_filter( 'wporg_block_navigation_menus', __NAMESPACE__ . '\add_site_navigation_menus' );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo esc_html( $html_title ); ?></title>
		<?php
		wp_head();
		add_social_tags( $html_title, $url, $html_description, $image_url );
		?>
	</head>

	<body <?php body_class(); ?>>
	<?php
	wp_body_open();
	Templates::part( 'header.html', array() );
	?>
<?php
/**
 * Provide a list with a navigation menu.
 *
 * @return array
 */
function add_site_navigation_menus(): array {
	return array(
		'site-header-menu' => array(
			array(
				'label' => esc_html__( 'Events', 'wporg-plugins' ),
				'url'   => 'https://translate.wordpress.org/events/',
			),
			array(
				'label' => esc_html__( 'Team', 'wporg-plugins' ),
				'url'   => 'https://make.wordpress.org/polyglots/teams/',
			),
			array(
				'label' => esc_html__( 'Requests', 'wporg-plugins' ),
				'url'   => 'https://make.wordpress.org/polyglots/?resolved=unresolved',
			),
			array(
				'label' => esc_html__( 'Weekly Chats', 'wporg-plugins' ),
				'url'   => 'https://make.wordpress.org/polyglots/category/weekly-chats/',
			),
			array(
				'label' => esc_html__( 'Translate', 'wporg-plugins' ),
				'url'   => 'https://translate.wordpress.org/',
			),
			array(
				'label' => esc_html__( 'Handbook', 'wporg-plugins' ),
				'url'   => 'https://make.wordpress.org/polyglots/handbook/',
			),
		),
	);
}

/**
 * Add social tags to the head of the page.
 *
 * @param string $html_title       The title of the page.
 * @param string $url              The URL of the page.
 * @param string $html_description The description of the page.
 * @param string $image_url        The URL of the image to use.
 *
 * @return void
 */
function add_social_tags( string $html_title, string $url, string $html_description, string $image_url ) {
	$meta_tags = array(
		'name'     => array(
			'twitter:card'        => 'summary',
			'twitter:site'        => '@WordPress',
			'twitter:title'       => esc_attr( $html_title ),
			'twitter:description' => esc_attr( $html_description ),
			'twitter:creator'     => '@WordPress',
			'twitter:image'       => esc_url( $image_url ),
			'twitter:image:alt'   => esc_attr( $html_title ),
		),
		'property' => array(
			'og:url'              => esc_url( $url ),
			'og:title'            => esc_attr( $html_title ),
			'og:description'      => esc_attr( $html_description ),
			'og:site_name'        => esc_attr( get_bloginfo() ),
			'og:image:url'        => esc_url( $image_url ),
			'og:image:secure_url' => esc_url( $image_url ),
			'og:image:type'       => 'image/png',
			'og:image:width'      => '1200',
			'og:image:height'     => '675',
			'og:image:alt'        => esc_attr( $html_title ),
		),
	);

	foreach ( $meta_tags as $name => $content ) {
		foreach ( $content as $key => $value ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<meta ' . esc_attr( $name ) . '="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '" />' . "\n";
		}
	}
}
