<?php
/**
 * Plugin Name:       Post Bulk Actions
 * Description:       Open multiple selected posts/pages in new editor tabs for efficient bulk editing.
 * Version:           1.0.0
 * Author:            Jorge Araya
 * Author URI:        https://jorgearaya.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-bulk-actions
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * Network:           false
 *
 * Post Bulk Actions is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * @package Post_Bulk_Actions
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Define plugin constants.
define( 'PBA_VERSION', '1.0.0' );
define( 'PBA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PBA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Autoload or manually require.
require_once PBA_PLUGIN_PATH . 'includes/class-post-bulk-actions.php';

// Initialize.
new Post_Bulk_Actions();

// Hooks.
register_activation_hook( __FILE__, 'pba_activation_hook' );
register_uninstall_hook( __FILE__, 'pba_uninstall_hook' );

/**
 * Plugin activation hook
 */
function pba_activation_hook() {
	if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) || version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html( 'This plugin requires WordPress 5.0+ and PHP 7.4+', 'post-bulk-actions' ) );
	}
}

/**
 * Plugin uninstall hook
 */
function pba_uninstall_hook() {
	// Nothing to clean up.
}
