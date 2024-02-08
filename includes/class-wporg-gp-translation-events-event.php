<?php

class WPORG_GP_Translation_Events_Event {
	private int $id;
	private DateTimeImmutable $start;
	private DateTimeImmutable $end;
	private DateTimeZone $timezone;

	public function __construct( int $id, DateTimeImmutable $start, DateTimeImmutable $end, DateTimeZone $timezone ) {
		$this->id       = $id;
		$this->start    = $start;
		$this->end      = $end;
		$this->timezone = $timezone;
	}

	public function id(): int {
		return $this->id;
	}

	public function start(): DateTimeImmutable {
		return $this->start;
	}

	public function end(): DateTimeImmutable {
		return $this->end;
	}

	public function timezone(): DateTimeZone {
		return $this->timezone;
	}
}
