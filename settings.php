<?php
namespace Podlove\Beta;

add_action(settings_hook(), '\Podlove\Beta\add_menu_entry');
add_filter('plugin_row_meta', '\Podlove\Beta\add_settings_link_to_plugin_meta', 10, 4);
add_action('admin_init', '\Podlove\Beta\handle_settings_request');
add_action('admin_init', '\Podlove\Beta\register_admin_styles');

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
	wp_register_style('podlove-beta-admin-style', plugins_url('css/admin.css', __FILE__));
}

function add_settings_link_to_plugin_meta($plugin_meta, $plugin_file, $plugin_data, $status) {

	if ($plugin_file !== 'podlove-beta-tester/podlove-beta-tester.php')
		return $plugin_meta;

	$plugin_meta[] = '<a href="' . settings_url() . '">' . __('Settings') . '</a>';

	return $plugin_meta;
}

function handle_settings_request() {
	$action = filter_input(INPUT_GET, 'action');

	if (!$action)
		return;

	switch ($action) {
		case 'switch_branch':
			if (!$branch = filter_input(INPUT_GET, 'branch'))
				return;

			// @todo validate branch
			update_option('podlove_beta_branch', $branch);
			break;
		case 'leave_branch':
			delete_option('podlove_beta_branch');
			break;
		default:
			return;
	}

	wp_safe_redirect(settings_url());
}

function settings_page() {
	$config = new Config;
	$current_branch = get_option('podlove_beta_branch');

	?>
	<div class="wrap">
		<h2><?php echo __("Podlove Beta Tester", 'podlove-beta-tester') ?></h2>	

		<?php foreach ($config->plugins() as $plugin): ?>
			<div class="card">
				<h3><?php echo $plugin->title ?></h3>
				<div class="branch-status <?php echo empty($current_branch) ? 'active' : '' ?>">
					<p>
						<em>
							<?php if ($current_branch): ?>
								Tracking development branch "<?php echo $current_branch ?>"
							<?php else: ?>
								You are getting stable updates via WordPress Plugin Directory.
							<?php endif ?>
						</em>
					</p>	
				</div>
				<?php foreach ($plugin->branches() as $branch): ?>
					<?php $is_active = $branch->title === $current_branch; ?>
					<hr/>
					<section class="branch <?php echo $is_active ? 'active' : '' ?>">
						<header>
							<h4><?php echo $branch->title ?></h4>
							<?php if ($is_active): ?>
								<?php echo leave_branch_link() ?>
							<?php else: ?>
								<?php echo switch_branch_link($branch->title) ?>
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
	</div>
	<?php
}

/**
 * HTML link to leave the current branch
 * 
 * @return string
 */
function leave_branch_link() {
	return sprintf(
		'<a href="%s">%s</a>', 
		leave_branch_url(), 
		__('Switch back to stable Version', 'podlove-beta-tester')
	);
}

/**
 * URL to leave the current branch
 * 
 * @return string
 */
function leave_branch_url() {
	return add_query_arg(['action' => 'leave_branch'], settings_url());
}

/**
 * HTML link to switch to another branch
 * 
 * @param string $branch
 * @return string
 */
function switch_branch_link($branch) {
	return sprintf(
		'<a href="%s">%s</a>', 
		switch_branch_url($branch), 
		__('Switch to this Version', 'podlove-beta-tester')
	);
}

/**
 * URL to switch to another branch
 * 
 * @param string $branch
 * @return string
 */
function switch_branch_url($branch) {
	return add_query_arg(['action' => 'switch_branch', 'branch' => $branch], settings_url());
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