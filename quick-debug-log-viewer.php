<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpsani.store
 * @since             1.0.0
 * @package           Quick_Debug_Log_Viewer
 *
 * @wordpress-plugin
 * Plugin Name:       Quick debug.log Viewer
 * Description:       A simple and lightweight plugin to view, clear and download the debug.log file in WordPress.
 * Version:           1.2.0
 * Author:            WP Sani
 * Author URI:        https://wpsani.store/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quick-debug-log-viewer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'QUICK_DEBUG_LOG_VIEWER_VERSION', '1.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quick-debug-log-viewer-activator.php
 */
function quick_debug_log_viewer_activate() {
	$activator_path = plugin_dir_path( __FILE__ ) . 'includes/class-quick-debug-log-viewer-activator.php';
	if ( file_exists( $activator_path ) ) {
		require_once $activator_path;
		Quick_Debug_Log_Viewer_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quick-debug-log-viewer-deactivator.php
 */
function quick_debug_log_viewer_deactivate() {
	$deactivator_path = plugin_dir_path( __FILE__ ) . 'includes/class-quick-debug-log-viewer-deactivator.php';
	if ( file_exists( $deactivator_path ) ) {
		require_once $deactivator_path;
		Quick_Debug_Log_Viewer_Deactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'quick_debug_log_viewer_activate' );
register_deactivation_hook( __FILE__, 'quick_debug_log_viewer_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-quick-debug-log-viewer.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function quick_debug_log_viewer_run() {

	$plugin = new Quick_Debug_Log_Viewer();
	$plugin->run();

}
quick_debug_log_viewer_run();
