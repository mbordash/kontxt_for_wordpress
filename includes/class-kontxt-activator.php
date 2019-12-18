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


	private $api_host_only;
	private $api_host_uri;
	private $api_host_proto;
	private $api_host_port;
	private $plugin_name;
	private $version;
	private $option_name;
	private $api_host;

	/**
	 * Kontxt_Public constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $option_name
	 * @param $api_host
	 * @param $api_host_only
	 * @param $api_host_uri
	 * @param $api_host_proto
	 * @param $api_host_port
	 */
	public function __construct( $plugin_name, $version, $option_name, $api_host, $api_host_only, $api_host_uri, $api_host_proto, $api_host_port )
	{

		$this->plugin_name      = $plugin_name;
		$this->version          = $version;
		$this->option_name      = $option_name;
		$this->api_host         = $api_host;
		$this->api_host_only    = $api_host_only;
		$this->api_host_uri     = $api_host_uri;
		$this->api_host_proto   = $api_host_proto;
		$this->api_host_port    = $api_host_port;

	}


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @return false|string
	 * @since    1.0.0
	 */
	public function activate() {

		// first check to make sure the KONTXT settings are already set in wordpress options
		// this is in case the customer de/re activated the plugin and we don't overwrite the uid/key

		$apiKey = get_option( $this->option_name . '_apikey' );
		$apiUid = get_option( $this->option_name . '_apiuid' );

		if( !isset($apiKey) || $apiKey === '' ) {

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

			// error_log(print_r($requestBody, TRUE));

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			$response = wp_remote_get($api_host, $opts);

			if( $response['response']['code'] === 200 ) {

				$apiKey = str_replace( '"', '', $response['body']);

				update_option( $this->option_name .  '_apiuid', $apiUid);
				update_option( $this->option_name .  '_apikey', $apiKey);

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Install Error. Something went wrong with this request. Code received: " . $response['response']['code'];

				return json_encode($response_array);

			}

		}

	}

}
