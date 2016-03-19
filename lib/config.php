<?php
namespace Podlove\Beta;

class Config {

	public static function data()
	{
		$empty_data = ((object) ['plugins' => []]);

		if (false === ($config = get_transient('podlove_beta_config'))) {
			
			// $config = wp_remote_fopen("https://eric.co.de/releases/config.json");

			$uri = "https://eric.co.de/releases/config.json";
			// BEGIN wp_remote_fopen
			$parsed_url = @parse_url( $uri );
			
			if ( !$parsed_url || !is_array( $parsed_url ) )
			        return $empty_data;
			
			$options = array();
			$options['timeout'] = 10;
			$options['sslverify'] = true;
			
			$response = wp_safe_remote_get( $uri, $options );
			
			if (is_wp_error($response)) {
				error_log(print_r($response->get_error_message() . ' in ' . __FILE__ . ' line ' . __LINE__, true));
				return $empty_data;
			}
			
			$config = wp_remote_retrieve_body( $response );
			// END wp_remote_fopen

			if ($config === false) {
				$config = $empty_data;
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
