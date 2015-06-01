<?php
namespace Podlove\Beta;

class Config {

	public static function data()
	{
		return [
			'plugins' => [
				'plugin-deployment-demo' => [
					'title' => 'Plugin Deployment Demo',
					'file'  => 'plugin-deployment-demo/plugin-deployment-demo.php',
					'branches' => [
						'development' => [
							'state'       => 'stable',
							'description' => 'Stable development stuffz.'
						],
						'experiment' => [
							'state'       => 'alpha',
							'description' => 'Watch ou—*\b00m/*'
						]
					]
				],
				'podlove-podcasting-plugin-for-wordpress' => [
					'title' => 'Podlove Podcast Publisher',
					'file'  => 'podlove-podcasting-plugin-for-wordpress/podlove.php',
					'branches' => [
						'release-2-1-4' => [
							'state' => 'stable',
							'description' => 'Bugfixes for 2.1 Release'
						],
						'release-2-2-0' => [
							'state' => 'alpha',
							'description' => 'Working on: Image caching & resizing'
						],
						'experimental-dist' => [
							'state' => 'alpha'
						]
					]
				]
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

}
