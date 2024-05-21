<?php

namespace Wporg\TranslationEvents\Routes\Event;

use DateTimeImmutable;
use DateTimeZone;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\Event_End_Date;
use Wporg\TranslationEvents\Event\Event_Start_Date;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Translation_Events;

/**
 * Displays the image for the event.
 */
class Image_Route extends Route {

	/**
	 * @var Event_Repository_Interface
	 */
	private Event_Repository_Interface $event_repository;

	/**
	 * Image_Route constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->event_repository = Translation_Events::get_event_repository();
	}

	/**
	 * Handles the request.
	 *
	 * @param int $event_id The event ID.
	 */
	public function handle( int $event_id ): void {
		if ( ! extension_loaded( 'gd' ) ) {
			$this->die_with_error( esc_html__( 'The image cannot be generated because GD extension is not installed.', 'gp-translation-events' ) );
		}
		$event = $this->event_repository->get_event( $event_id );
		if ( ! $event ) {
			$this->die_with_404();
		}

		$image    = imagecreatetruecolor( 1200, 675 );
		$bg_color = imagecolorallocate( $image, 35, 40, 45 );
		imagefill( $image, 0, 0, $bg_color );
		$text_color = imagecolorallocate( $image, 255, 255, 255 );
		$text       = substr( $event->title(), 0, 20 );
		$font       = trailingslashit( dirname( __DIR__, 3 ) ) . 'assets/fonts/eb-garamond/EBGaramond-Regular.ttf';
		$text_size  = 70;
		$text_angle = 0;
		$text_box   = imagettfbbox( $text_size, $text_angle, $font, $text );
		$text_width = $text_box[4] - $text_box[0];

		$text_x = ( 1200 - $text_width ) / 2;
		$text_y = 400;

		imagettftext( $image, $text_size, $text_angle, $text_x, $text_y, $text_color, $font, $text );

		header( 'Content-type: image/png' );
		imagepng( $image );
		imagedestroy( $image );
	}
}
