<?php

namespace Wporg\TranslationEvents;

class Assets {
	private string $base_dir;
	private bool $use_new_design;

	public function __construct() {
		$this->base_dir       = realpath( __DIR__ . '/../assets' );
		$this->use_new_design = false;
	}

	public function use_new_design(): void {
		$this->use_new_design = true;
	}

	public function enqueue(): void {
	}
}
