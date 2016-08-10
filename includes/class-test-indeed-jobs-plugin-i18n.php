<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.indeed.com
 * @since      1.0.0
 *
 * @package    Test_Indeed_Jobs_Plugin
 * @subpackage Test_Indeed_Jobs_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Test_Indeed_Jobs_Plugin
 * @subpackage Test_Indeed_Jobs_Plugin/includes
 * @author     Indeed Hackathon Team <rdowns@indeed.com>
 */
class Test_Indeed_Jobs_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'test-indeed-jobs-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
