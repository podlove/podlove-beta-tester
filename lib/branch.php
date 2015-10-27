<?php
namespace Podlove\Beta;

class Branch {

	public $title;
	public $state;
	public $description;

	public function __construct($title, $state, $description) {
		$this->title = $title;
		$this->state = $state;
		$this->description = $description;
	}

	public function stateText() {
		return self::states()[$this->state];
	}

	private static function states() {
		return [
			'stable' => 'Should be safe to use.',
			'rc'     => 'No new features are introduced, only looking for bugs.',
			'beta'   => 'Most planned features are implemented, ready for testing.',
			'alpha'  => 'Work in progress, handle with great care.',
			'patch'  => 'Bugfixes and minor changes.'
		];
	}

}
