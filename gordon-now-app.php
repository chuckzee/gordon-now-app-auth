<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gfs.com
 * @since             1.0.0
 * @package           Gordon_Now_App
 *
 * @wordpress-plugin
 * Plugin Name:       Gordon Now App Integration
 * Plugin URI:        https://gordonnow.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Chuck Zimmerman
 * Author URI:        https://gfs.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gordon-now-app
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GORDON_NOW_APP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gordon-now-app-activator.php
 */
function activate_gordon_now_app() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gordon-now-app-activator.php';
	Gordon_Now_App_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gordon-now-app-deactivator.php
 */
function deactivate_gordon_now_app() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gordon-now-app-deactivator.php';
	Gordon_Now_App_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gordon_now_app' );
register_deactivation_hook( __FILE__, 'deactivate_gordon_now_app' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gordon-now-app.php';

/**
 * Require composer autoload
 */

require 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gordon_now_app() {

	$plugin = new Gordon_Now_App();
	$plugin->run();

}
run_gordon_now_app();
