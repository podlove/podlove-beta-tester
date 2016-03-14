<?php
/**
 * Plugin Name: Podlove Beta Tester
 * Plugin URI:  http://podlove.org
 * Description: Get plugin updates for Podlove plugins before they are stable. Supported plugins: Podlove Publisher
 * Version:     1.1.0
 * Author:      Podlove
 * Author URI:  http://podlove.org
 * License:     MIT
 * License URI: license.txt
 * Text Domain: podlove-beta-tester
 */

$correct_php_version = version_compare( phpversion(), "5.4", ">=" );

if ( ! $correct_php_version ) {
	echo "Podlove Beta Tester requires <strong>PHP 5.4</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

require_once 'inc/bootstrap.php';
