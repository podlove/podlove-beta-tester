<?php
require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/branch.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/config.php';
require_once plugin_dir_path( __FILE__ ) . '../lib/plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'settings.php';

require 'plugin-update-checker/plugin-update-checker.php';

add_action('upgrader_process_complete', 'podlove_beta_update_plugin_branch_state', 10, 2);

// don't activate while switching branches
if (!in_array(filter_input(INPUT_GET, 'action'), ['switch_branch', 'leave_branch'])) {
	add_action('plugins_loaded', 'podlove_beta_setup_plugin_update_server');
}

// always update beta plugin through itself
add_filter('option_podlove_beta_next_branch', 'podlove_add_beta_tester_branch_to_config');
add_filter('option_podlove_beta_current_branch', 'podlove_add_beta_tester_branch_to_config');

function podlove_add_beta_tester_branch_to_config($branches) {

	if (!isset($branches['podlove-beta-tester']))
		$branches['podlove-beta-tester'] = 'master';

	return $branches;
}

/**
 * Setup plugin update Server
 * 
 * Using "Plugin Update Checker" API, change update server for active beta plugins.
 */
function podlove_beta_setup_plugin_update_server() {
	
	if (!is_admin())
		return;

	include_once(ABSPATH . 'wp-admin/includes/plugin.php');

	$config = new \Podlove\Beta\Config;
	$next_branch = get_option('podlove_beta_next_branch', []);

	foreach ($config->plugins() as $plugin) {
		$branch = isset($next_branch[$plugin->slug]) ? $next_branch[$plugin->slug] : NULL;
		
		if (is_plugin_active($plugin->file) && $branch) {
			
			$update_url = sprintf(
		    	'https://eric.co.de/releases/?action=get_metadata&slug=%s&branch=%s',
		    	$plugin->slug,
		    	$branch
		    );

			PucFactory::buildUpdateChecker($update_url, $plugin->absolute_file_path());
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

	if (!isset($params['plugins']) || !is_array($params['plugins']))
		return;

	foreach ($params['plugins'] as $plugin_file) {
		if ($slug = \Podlove\Beta\Config::plugin_slug_for_filename($plugin_file)) {
			$current_branch[$slug] = $next_branch[$slug];
		}
	}

	update_option('podlove_beta_current_branch', $current_branch);
}