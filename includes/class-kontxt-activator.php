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

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @return false|string
	 * @since    1.0.0
	 */
	public static function activate() {

		$kontxt_ini = parse_ini_file(plugin_dir_path( __FILE__ ) . '../app.ini.php' );

		$option_name    = 'KONTXT';
		$application    = 'WordPress Semantic Engine';
		$api_host       = $kontxt_ini['api_host'];
		$api_path       = 'site';

		// first check to make sure the KONTXT settings are already set in wordpress options
		// this is in case the customer de/re activated the plugin and we don't overwrite the uid/key

		$apiKey = get_option( $option_name . '_apikey' );
		$apiUid = get_option( $option_name . '_apiuid' );

		if( !isset($apiKey) || $apiKey === '' || $apiKey === false) {

			// install site and get a key from kontxt

			$siteName   = get_bloginfo( 'name' );
			$siteDomain = get_bloginfo( 'url' );
			$siteEmail  = get_bloginfo( 'admin_email' );
			$apiUid     = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			$service    = 'install';

			// register with KONTXT Site API endpoint
			$requestBody = array(
				'api_uid'                   => $apiUid,
				'application'               => $application,
				'site_name'                 => $siteName,
				'site_domain'               => $siteDomain,
				'site_email'                => $siteEmail,
			);

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			$response = wp_remote_get($api_host . '/' . $api_path . '/' . $service, $opts );

			if( $response['response']['code'] === 200 ) {

				$apiKey = str_replace( '"', '', $response['body']);

				update_option( $option_name . '_apikey', $apiKey);
				update_option( $option_name . '_apiuid', $apiUid);
				update_option( $option_name . '_optin', 'yes');

				return true;

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Install Error. Something went wrong with this request. Code received: " . $response['response']['code'];

				return json_encode($response_array);
			}

		} else {

			// update site

			$siteName   = get_bloginfo( 'name' );
			$siteDomain = get_bloginfo( 'url' );
			$siteEmail  = get_bloginfo( 'admin_email' );
			$service    = 'update';

			// register with KONTXT Site API endpoint
			$requestBody = array(

				'api_uid'       => $apiUid,
				'api_key'       => $apiKey,
				'application'   => $application,
				'site_name'     => $siteName,
				'site_domain'   => $siteDomain,
				'site_email'    => $siteEmail
			);

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			wp_remote_get($api_host . '/' . $api_path . '/' . $service, $opts );

			return true;

		}
	}
}
