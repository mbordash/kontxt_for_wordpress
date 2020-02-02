<?php

/**
 *
 * @link              https://www.kontxt.com
 * @since             1.0.0
 * @package           Kontxt
 *
 * @wordpress-plugin
 * Plugin Name:       KONTXT Semantic Engine
 * Plugin URI:        https://www.kontxt.com
 * Description:       KONTXT™ Semantic Engine is an AI-backed content analyzer and recommendation plugin providing valuable insights about your customers’ interactions (search queries, chat dialogs and customer service questions). Powered by Kontxt™ state of the art Natural Language Processing machine learning system.
 * Version:           1.0.11
 * Author:            RealNetworks KONTXT
 * Author URI:        https://kontxt.com/kontxt-semantic-engine-for-retail/
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
function activate_kontxt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-activator.php';
	Kontxt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kontxt-deactivator.php
 */

function deactivate_kontxt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-deactivator.php';
	Kontxt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kontxt' );
register_deactivation_hook( __FILE__, 'deactivate_kontxt' );

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
