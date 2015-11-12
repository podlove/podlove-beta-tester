<?php
namespace Podlove\Beta;

class Config {

	public static function data()
	{
		return [
			'plugins' => [
				'podlove-beta-tester' => [
					'hidden' => true,
					'title' => 'Podlove Beta Tester',
					'file'  => 'podlove-beta-tester/podlove-beta-tester.php',
					'branches' => [
						'master' => []
					]
				],
				'podlove-podcasting-plugin-for-wordpress' => [
					'title' => 'Podlove Podcast Publisher',
					'file'  => 'podlove-podcasting-plugin-for-wordpress/podlove.php',
					'branches' => [
						'release-2-3-3' => [
							'state' => 'patch',
							'description' => 'Minor changes following the 2.3 Release.'
						]
					]
				]
				// 'plugin-deployment-demo' => [
				// 	'title' => 'Plugin Deployment Demo',
				// 	'file'  => 'plugin-deployment-demo/plugin-deployment-demo.php',
				// 	'branches' => [
				// 		'development' => [
				// 			'state'       => 'stable',
				// 			'description' => 'Stable development stuffz.'
				// 		],
				// 		'experiment' => [
				// 			'state'       => 'alpha',
				// 			'description' => 'Watch ou—*\b00m/*'
				// 		]
				// 	]
				// ]
			]
		];
	}

	public static function plugin_slug_for_filename($filename) {
		$plugin_data = self::data()['plugins'];
		foreach ($plugin_data as $slug => $plugin) {
			if ($plugin['file'] === $filename) {
				return $slug;
			}
		}

		return null;
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

	public function plugin($slug)
	{
		$plugin_data = self::data()['plugins'];

		if (isset($plugin_data[$slug])) {
			return new Plugin($slug, $plugin_data[$slug]);
		}

		return null;
	}

}
