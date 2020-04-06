<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://gfs.com
 * @since      1.0.0
 *
 * @package    Gordon_Now_App
 * @subpackage Gordon_Now_App/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gordon_Now_App
 * @subpackage Gordon_Now_App/includes
 * @author     Chuck Zimmerman <chuck.zimmerman@gfs.com>
 */
class Gordon_Now_App_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gordon-now-app',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
