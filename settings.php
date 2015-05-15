<?php
namespace Podlove\Beta;

add_action(settings_hook(), '\Podlove\Beta\add_menu_entry');
add_filter('plugin_row_meta', '\Podlove\Beta\add_settings_link_to_plugin_meta', 10, 4);

/**
 * Add settings menu entry.
 * 
 * If we have a Multisite, add to network settings, otherwise blog settings.
 */
function add_menu_entry() {
 	add_submenu_page(
 		settings_slug(),
 		__("Podlove Beta Tester", 'podlove-beta-tester'),
 		__("Podlove Beta Tester", 'podlove-beta-tester'), 
 		'activate_plugins',
 		'podlove-beta-tester',
 		'\Podlove\Beta\settings_page'
 	);
}

function add_settings_link_to_plugin_meta($plugin_meta, $plugin_file, $plugin_data, $status) {

	if ($plugin_file !== 'podlove-beta-tester/podlove-beta-tester.php')
		return $plugin_meta;

	$plugin_meta[] = '<a href="' . settings_url() . '">' . __('Settings') . '</a>';

	return $plugin_meta;
}

function settings_page() {
	?>
	<div class="wrap">
		<h2><?php echo __("Podlove Beta Tester", 'podlove-beta-tester') ?></h2>	
	</div>
	<?php
}

/**
 * Returns admin settings url depending on if we have a Multisite.
 * 
 * @return string
 */
function settings_url() {
	$url = settings_slug() . '?page=podlove-beta-tester';
	return is_multisite() ? network_admin_url($url) : admin_url($url);
}

/**
 * Returns menu settings slug depending on if we have a Multisite.
 * 
 * @return string
 */
function settings_slug() {
	return is_multisite() ? 'settings.php' : 'options-general.php';
}

/**
 * Returns menu settings hook depending on if we have a Multisite.
 * 
 * @return string
 */
function settings_hook() {
	return is_multisite() ? 'network_admin_menu' : 'admin_menu';
}