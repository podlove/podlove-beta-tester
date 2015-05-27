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
			'alpha'  => 'Work in progress, handle with great care.'
		];
	}

}

class Plugin {

	public $slug;
	public $title;

	private $branches;

	public function __construct($slug, $data) {
		$this->slug = $slug;
		$this->title = $data['title'];

		$this->branches = [];
		foreach ($data['branches'] as $branch_slug => $branch) {
			$this->branches[] = new Branch($branch_slug, $branch['state'], $branch['description']);
		}
	}

	public function branches() {
		return $this->branches;
	}

}

class Config {

	public static function data()
	{
		return [
			'plugins' => [
				'podlove-podcasting-plugin-for-wordpress' => [
					'title' => 'Podlove Podcast Publisher',
					'branches' => [
						// 'master' => [
						// 	'state' => 'stable', 
						// 	'description' => 'The "master" branch is used for releases. You basically get the same as without the Beta plugin.'
						// ],
						'release-2-1-4' => [
							'state' => 'stable',
							'description' => 'Bugfixes for 2.1 Release'
						],
						'release-2-2-0' => [
							'state' => 'alpha',
							'description' => 'Working on: Image caching & resizing'
						]
					]
				]
			]
		];
	}

	public function plugins()
	{
		$plugin_data = self::data()['plugins'];
		$plugins = [];

		foreach ($plugin_data as $plugin_slug => $plugin) {
			$plugins[] = new Plugin($plugin_slug, $plugin);
		}

		return $plugins;
	}

}

