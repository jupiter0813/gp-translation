<?php

namespace Wporg\Tests\Event;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

class Event_Image_Test extends GP_UnitTestCase {
	/**
	 * Test that when the header template is fired, it generates the social metadata.
	 *
	 * @return void
	 */
	public function test_gp_head_metadata() {
		$html_title       = 'Test HTML Title';
		$url              = URLS::events_home();
		$html_description = 'This is a test description of the page.';
		$image_url        = URLs::event_default_image();
		$page_title       = 'Test Page Title';
		Templates::header(
			array(
				'html_title'       => $html_title,
				'url'              => $url,
				'html_description' => $html_description,
				'image_url'        => $image_url,
				'page_title'       => $page_title,
			),
		);

		ob_start();
		do_action( 'gp_head' );
		$output = ob_get_clean();

		$this->assertStringContainsString( '<meta name="twitter:card" content="summary" />', $output );
		$this->assertStringContainsString( '<meta name="twitter:site" content="@WordPress" />', $output );
		$this->assertStringContainsString( '<meta name="twitter:title" content="' . $html_title . '" />', $output );
		$this->assertStringContainsString( '<meta name="twitter:description" content="' . $html_description . '" />', $output );
		$this->assertStringContainsString( '<meta name="twitter:image" content="' . $image_url . '" />', $output );
		$this->assertStringContainsString( '<meta name="twitter:image:alt" content="' . $html_title . '" />', $output );

		$this->assertStringContainsString( '<meta property="og:url" content="' . $url . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:title" content="' . $html_title . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:description" content="' . $html_description . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:site_name" content="' . esc_attr( get_bloginfo() ) . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:url" content="' . $image_url . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:secure_url" content="' . $image_url . '" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:type" content="image/png" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:width" content="1200" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:height" content="675" />', $output );
		$this->assertStringContainsString( '<meta property="og:image:alt" content="' . $html_title . '" />', $output );
	}
}
