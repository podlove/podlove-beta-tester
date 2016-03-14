<?php
namespace Podlove\Beta;

add_action(settings_hook(), '\Podlove\Beta\add_menu_entry');
add_filter('plugin_row_meta', '\Podlove\Beta\add_settings_link_to_plugin_meta', 10, 4);
add_action('admin_init', '\Podlove\Beta\handle_settings_request');
add_action('admin_init', '\Podlove\Beta\register_admin_styles');

add_action('podlove_beta_before_switch_branch', '\Podlove\Beta\backup_current_plugin', 10, 2);
add_action('podlove_beta_after_leave_branch', '\Podlove\Beta\restore_stable_plugin');
add_action('podlove_beta_switch_branch', '\Podlove\Beta\switch_branch', 10, 2);
add_action('podlove_beta_leave_branch' , '\Podlove\Beta\leave_branch');

/**
 * Add settings menu entry.
 * 
 * If we have a Multisite, add to network settings, otherwise blog settings.
 */
function add_menu_entry() {
 	$page = add_submenu_page(
 		settings_slug(),
 		__("Podlove Beta Tester", 'podlove-beta-tester'),
 		__("Podlove Beta Tester", 'podlove-beta-tester'), 
 		'activate_plugins',
 		'podlove-beta-tester',
 		'\Podlove\Beta\settings_page'
 	);

 	add_action('admin_print_styles-' . $page, function () {
 		wp_enqueue_style('podlove-beta-admin-style');
 	});
}

/**
 * Register Admin CSS Stylesheet
 */
function register_admin_styles() {
	wp_register_style('podlove-beta-admin-style', plugins_url('../css/admin.css', __FILE__));
}

function add_settings_link_to_plugin_meta($plugin_meta, $plugin_file, $plugin_data, $status) {

	if ($plugin_file !== 'podlove-beta-tester/podlove-beta-tester.php')
		return $plugin_meta;

	$plugin_meta[] = '<a href="' . settings_url() . '">' . __('Settings') . '</a>';

	return $plugin_meta;
}

/**
 * Backup current plugin so it can be restored later.
 * 
 * @param  string $plugin_slug Plugin identifier.
 * @param  string $branch Branch identifier.
 */
function backup_current_plugin($plugin_slug, $branch) {
	global $wp_filesystem;

	// initialize filesystem if necessary
	if (!$wp_filesystem)
		\WP_Filesystem();

	$config     = new \Podlove\Beta\Config;
	$plugin     = $config->plugin($plugin_slug);
	$plugin_dir = $plugin->absolute_dir_path();

	$target = ABSPATH . 'wp-content/podlove-beta-backup/' . $plugin_slug;
	wp_mkdir_p($target);
	copy_dir($plugin_dir, $target);
}

/**
 * Restore stable plugin files if backup is available.
 * 
 * @param  string $plugin_slug Plugin identifier.
 */
function restore_stable_plugin($plugin_slug) {
	global $wp_filesystem;

	// initialize filesystem if necessary
	if (!$wp_filesystem)
		\WP_Filesystem();

	$config     = new \Podlove\Beta\Config;
	$plugin     = $config->plugin($plugin_slug);
	$plugin_dir = $plugin->absolute_dir_path();
	$backup     = ABSPATH . 'wp-content/podlove-beta-backup/' . $plugin_slug;

	// abort if backup does not exist
	if (!file_exists($backup))
		return;

	copy_dir($backup, $plugin_dir);
}

/**
 * Switch plugin updater to given branch.
 * 
 * @param  string $plugin Plugin identifier.
 * @param  string $branch Branch identifier.
 */
function switch_branch($plugin, $branch) {
	$next_branch = get_option('podlove_beta_next_branch', []);
	$next_branch[$plugin] = $branch;
	update_option('podlove_beta_next_branch', $next_branch);
}

/**
 * Switch plugin updater to stable releases.
 * 
 * @param  string $plugin Plugin identifier.
 */
function leave_branch($plugin) {
	$next_branch = get_option('podlove_beta_next_branch', []);
	unset($next_branch[$plugin]);
	update_option('podlove_beta_next_branch', $next_branch);
}

function handle_settings_request() {

	$action = filter_input(INPUT_GET, 'action');
	$plugin = filter_input(INPUT_GET, 'plugin');

	if (!$action)
		return;

	switch ($action) {
		case 'switch_branch':
			if (!$branch = filter_input(INPUT_GET, 'branch'))
				return;

			do_action('podlove_beta_before_switch_branch', $plugin, $branch);
			do_action('podlove_beta_switch_branch',        $plugin, $branch);
			do_action('podlove_beta_after_switch_branch',  $plugin, $branch);
			break;
		case 'leave_branch':
			do_action('podlove_beta_before_leave_branch', $plugin);
			do_action('podlove_beta_leave_branch',        $plugin);
			do_action('podlove_beta_after_leave_branch',  $plugin);
			break;
		case 'reload_config';
			$x = delete_transient('podlove_beta_config');
			wp_redirect(settings_url());
			exit;
			break;
		default:
			return;
	}

	// clear plugin-update cache after changing branches
	$config = new \Podlove\Beta\Config;
	foreach ($config->plugins() as $plugin) {
		delete_site_option('external_updates-' . $plugin->slug);
	}

	delete_site_transient('update_plugins');

	wp_safe_redirect(settings_url());
}

function settings_page() {
	$config = new Config;
	$next_branch    = get_option('podlove_beta_next_branch');
	$current_branch = get_option('podlove_beta_current_branch');

	?>
	<div class="wrap">
		<h2><?php echo __("Podlove Beta Tester", 'podlove-beta-tester') ?></h2>	

		<?php foreach ($config->plugins() as $plugin): ?>
			<?php
			
			if ($plugin->hidden)
				continue;

			$next_plugin_branch    = isset($next_branch[$plugin->slug])    ? $next_branch[$plugin->slug]    : NULL;
			$current_plugin_branch = isset($current_branch[$plugin->slug]) ? $current_branch[$plugin->slug] : NULL;
			?>
			<div class="card">
				<h3><?php echo $plugin->title ?></h3>
				<div class="branch-status <?php echo $next_plugin_branch ? 'active' : '' ?>">
					<p>
						<em>
							<?php if ($next_plugin_branch): ?>
								Tracking development branch <strong>"<?php echo $next_plugin_branch ?>"</strong>
							<?php else: ?>
								You are getting stable updates via WordPress Plugin Directory.
							<?php endif ?>
						</em>
					</p>
				</div>
				<?php if ($current_plugin_branch != $next_plugin_branch): ?>
					<div class="branch-status update-notice">
						<p>
							<?php if (!empty($next_plugin_branch)): ?>
								You switched the version but you still need to download it. <a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">Go check for Updates</a>
							<?php else: ?>
								You switched the version back to stable but you might not see an update for it (if your latest beta version number is higher than the current stable version number). 
								The safest way is to download the stable plugin and override the beta plugin files.
							<?php endif ?>
						</p>
					</div>
				<?php endif ?>
				<?php foreach ($plugin->branches() as $branch): ?>
					<?php $is_active = $branch->title === $next_plugin_branch; ?>
					<hr/>
					<section class="branch <?php echo $is_active ? 'active' : '' ?>">
						<header>
							<h4><?php echo $branch->title ?></h4>
							<?php if ($is_active): ?>
								<?php echo leave_branch_link($plugin->slug) ?>
							<?php else: ?>
								<?php echo switch_branch_link($plugin->slug, $branch->title) ?>
							<?php endif ?>
						</header>
						<div class="clear"></div>
						<p>
							<?php echo $branch->description ?>
						</p>
						<em><?php echo $branch->state . ': ' . $branch->stateText() ?></em>
					</section>
				<?php endforeach ?>
			</div>			
		<?php endforeach ?>

		<p>
			<small><a href="<?php echo reload_config_url() ?>">Check if new beta branches are available</a></small>
		</p>
	</div>
	<?php
}

/**
 * HTML link to leave the current branch
 * 
 * @return string
 */
function leave_branch_link($plugin) {
	return sprintf(
		'<a href="%s">%s</a>', 
		leave_branch_url($plugin), 
		__('Switch back to stable Version', 'podlove-beta-tester')
	);
}

/**
 * URL to leave the current branch
 * 
 * @return string
 */
function leave_branch_url($plugin) {
	return add_query_arg(['action' => 'leave_branch', 'plugin' => $plugin], settings_url());
}

function reload_config_url() {
	return add_query_arg(['action' => 'reload_config'], settings_url());
}

/**
 * HTML link to switch to another branch
 * 
 * @param string $branch
 * @return string
 */
function switch_branch_link($plugin, $branch) {
	return sprintf(
		'<a href="%s">%s</a>', 
		switch_branch_url($plugin, $branch), 
		__('Switch to this Version', 'podlove-beta-tester')
	);
}

/**
 * URL to switch to another branch
 * 
 * @param string $branch
 * @return string
 */
function switch_branch_url($plugin, $branch) {
	return add_query_arg(['action' => 'switch_branch', 'plugin' => $plugin, 'branch' => $branch], settings_url());
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
