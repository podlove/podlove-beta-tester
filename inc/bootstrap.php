<?php
require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/branch.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/config.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'settings.php';

require 'plugin-update-checker/plugin-update-checker.php';

add_action('upgrader_process_complete', 'podlove_beta_update_plugin_branch_state', 10, 2);
add_action('plugins_loaded', 'podlove_beta_setup_plugin_update_server');

/**
 * Setup plugin update Server
 * 
 * Using "Plugin Update Checker" API, change update server for active beta plugins.
 */
function podlove_beta_setup_plugin_update_server() {
	
	$config = new \Podlove\Beta\Config;
	$next_branch = get_option('podlove_beta_next_branch', []);

	foreach ($config->plugins() as $plugin) {
		$branch = isset($next_branch[$plugin->slug]) ? $next_branch[$plugin->slug] : NULL;
		if (is_plugin_active($plugin->file) && $branch) {
			PucFactory::buildUpdateChecker(
			    sprintf(
			    	'http://eric.co.de/releases/?action=get_metadata&slug=%s&branch=%s',
			    	$plugin->slug,
			    	$branch
			    ),
			    trailingslashit(wp_normalize_path(WP_PLUGIN_DIR)) . $plugin->file
			);
		} else {
			// error_log(print_r("Plugin {$plugin->file} not active", true));
		}
	}
}

/**
 * Update plugin branch state after plugin upgrade
 * 
 * Hook doc {@see 'upgrader_process_complete'}
 * 
 * @param  array $upgrader
 * @param  array $params
 */
function podlove_beta_update_plugin_branch_state($upgrader, $params) {

	$current_branch = get_option('podlove_beta_current_branch', []);
	$next_branch    = get_option('podlove_beta_next_branch', []);

	foreach ($params['plugins'] as $plugin_file) {
		if ($slug = \Podlove\Beta\Config::plugin_slug_for_filename($plugin_file)) {
			$current_branch[$slug] = $next_branch[$slug];
		}
	}

	update_option('podlove_beta_current_branch', $current_branch);
}