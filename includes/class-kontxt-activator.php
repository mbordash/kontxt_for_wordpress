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
	public function activate() {

		error_log( 'Beginning Activation');

		$option_name    = 'KONTXT';
		//$api_host       = 'http://api.kontxt.cloud/wp-json/kontxt/v1/analyze';
		$api_host       = 'http://localhost/wp-json/kontxt/v1/analyze';


		// first check to make sure the KONTXT settings are already set in wordpress options
		// this is in case the customer de/re activated the plugin and we don't overwrite the uid/key

		$apiKey = get_option( $option_name . '_apikey' );
		$apiUid = get_option( $option_name . '_apiuid' );

		if( !isset($apiKey) || $apiKey === '' || $apiKey === false) {

			// install site and get a key from kontxt

			$siteName   = get_bloginfo( 'name' );
			$siteDomain = get_bloginfo( 'url' );
			$siteEmail  = get_bloginfo( 'admin_email' );
			$apiUid     = md5( $siteName . $siteDomain );
			$service    = 'install';

			// register with KONTXT Site API endpoint
			$requestBody = array(
				'api_uid'                   => $apiUid,
				'site_name'                 => $siteName,
				'site_domain'               => $siteDomain,
				'site_email'                => $siteEmail,
				'service'                   => $service
			);

			error_log( 'Request Body:: ' . print_r($requestBody, TRUE));

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			$response = wp_remote_get($api_host, $opts);

			error_log( 'Response Body:: ' . print_r($response, true) );


			if( $response['response']['code'] === 200 ) {

				$apiKey = str_replace( '"', '', $response['body']);

				update_option( $option_name . '_apikey', $apiKey);
				update_option( $option_name . '_apiuid', $apiUid);

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Install Error. Something went wrong with this request. Code received: " . $response['response']['code'];

				return json_encode($response_array);

			}

		}

	}

}
