<?php


/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kontxt
 * @subpackage Kontxt/activator
 * @author     Michael Bordash <michael@internetdj.com>
 */
class Kontxt_Activator {

	private $option_name    = 'KONTXT';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;

	}
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function activate() {

		// get site id, site name
		// register with KONTXT Site API endpoint

		register_setting( $this->plugin_name, $this->option_name . '_site_id', array( $this, $this->option_name . '_sanitize_text' ) );
		register_setting( $this->plugin_name, $this->option_name . '_apikey', array( $this, $this->option_name . '_sanitize_text' ) );


	}

}
