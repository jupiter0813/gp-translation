<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\InvalidStart;
use Wporg\TranslationEvents\Event\InvalidEnd;
use Wporg\TranslationEvents\Event\InvalidStatus;
use Wporg\TranslationEvents\Event\InvalidTitle;

class Event_Test extends WP_UnitTestCase {
	public function test_validates_start_and_end() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidEnd::class );
		new Event(
			0,
			$now,
			$now->modify( '-1 hours' ),
			$timezone,
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_start_and_end_timezone() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'Europe/Lisbon' ) );
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidStart::class );
		new Event(
			0,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_title() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidTitle::class );
		new Event(
			0,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'publish',
			'',
			'',
		);
	}

	public function test_validates_status() {
		$now      = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidStatus::class );
		new Event(
			0,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'',
			'Foo title',
			'',
		);
	}
}
