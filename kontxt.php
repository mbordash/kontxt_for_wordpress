<?php

/**
 *
 * @link              https://www.kontxt.com
 * @since             1.0.0
 * @package           Kontxt
 *
 * @wordpress-plugin
 * Plugin Name:       KONTXT Improves WordPress Search
 * Plugin URI:        https://www.kontxt.com
 * Description:       KONTXT™ improves search for both WordPress and WooCommerce sites. We use advanced machine learning and analytics to better understand the intent behind your customers' visit and use this data to present a better set of search results.  Plus, we included a Pinterest-style search results which has shown to better capture your visitors' attention and keep them coming back.
 * Version:           1.4.2
 * Author:            RealNetworks KONTXT
 * Author URI:        https://kontxt.com/kontxt-demand-engine-for-retail-2/
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       kontxt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kontxt-activator.php
 */
function kontxt_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-activator.php';
	Kontxt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kontxt-deactivator.php
 */

function kontxt_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-deactivator.php';
	Kontxt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'kontxt_activate' );
register_deactivation_hook( __FILE__, 'kontxt_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kontxt.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kontxt() {

	/**
	 *  The app ini file to set installation parameters for development or production
	 */
	$kontxt_ini = parse_ini_file(plugin_dir_path( __FILE__ ) . 'app.ini.php' );

	$plugin = new Kontxt($kontxt_ini);
	$plugin->run();

}
run_kontxt();
