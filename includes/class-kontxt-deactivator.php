<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.blobinator.com
 * @since      1.0.0
 *
 * @package    Blobinator
 * @subpackage Blobinator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kontxt
 * @subpackage Kontxt/includes
 * @author     Michael Bordash <michael@internetdj.com>
 */
class Kontxt_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		$kontxt_ini = parse_ini_file( plugin_dir_path( __FILE__ ) . '../app.ini.php' );

		$option_name = 'KONTXT';
		$api_host    = $kontxt_ini['api_host'];
		$api_path    = 'site';
		$service     = 'uninstall';

		// first check to make sure the KONTXT settings are already set in wordpress options
		// this is in case the customer de/re activated the plugin and we don't overwrite the uid/key

		$apiKey = get_option( $option_name . '_apikey' );
		$apiUid = get_option( $option_name . '_apiuid' );

		if ( $apiKey  ) {

			// register with KONTXT Site API endpoint
			$requestBody = array(
				'api_key' => $apiKey,
				'api_uid' => $apiUid,
			);

			$opts = array(
				'body'    => $requestBody,
				'headers' => 'Content-type: application/x-www-form-urlencoded'
			);

			wp_remote_get($api_host . '/' . $api_path . '/' . $service, $opts );

		}
	}

}
