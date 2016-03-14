<?php
namespace Podlove\Beta;

class Config {

	public static function data()
	{

		if (false === ($config = get_transient('podlove_beta_config'))) {
			
			$config = wp_remote_fopen("https://eric.co.de/releases/config.json");

			if ($config === false) {
				$config = [];
			} else {
				$config = json_decode($config);
			}

			// Put the results in a transient. Expire after 12 hours.
			set_transient('podlove_beta_config', $config, 12 * HOUR_IN_SECONDS);
		}

		return $config;
	}

	public static function plugin_slug_for_filename($filename) {
		$plugin_data = self::data()->plugins;
		foreach ($plugin_data as $slug => $plugin) {
			if ($plugin->file === $filename) {
				return $slug;
			}
		}

		return null;
	}

	public function plugins()
	{
		$plugin_data = self::data()->plugins;
		$plugins = [];

		foreach ($plugin_data as $plugin_slug => $plugin) {
			$plugins[] = new Plugin($plugin_slug, $plugin);
		}

		return $plugins;
	}

	public function plugin($slug)
	{
		$plugin_data = self::data()->plugins;

		if (isset($plugin_data->$slug)) {
			return new Plugin($slug, $plugin_data->$slug);
		}

		return null;
	}

}
