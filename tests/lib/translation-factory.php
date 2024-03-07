<?php

namespace Wporg\TranslationEvents\Tests;

use GP_UnitTest_Factory;

class Translation_Factory {
	private GP_UnitTest_Factory $gp_factory;
	private $set;

	public function __construct( GP_UnitTest_Factory $gp_factory ) {
		$this->gp_factory = $gp_factory;
		$this->set        = $this->gp_factory->translation_set->create_with_project_and_locale();
	}

	public function create( $user_id ) {
		$original = $this->gp_factory->original->create(
			array(
				'project_id' => $this->set->project->id,
				'status'     => '+active',
				'singular'   => 'foo',
			)
		);

		return $this->gp_factory->translation->create(
			array(
				'user_id'            => $user_id,
				'translation_set_id' => $this->set->id,
				'original_id'        => $original->id,
				'status'             => 'waiting',
			)
		);
	}
}
