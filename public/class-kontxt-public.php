<?php

/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kontxt
 * @subpackage Kontxt/admin
 * @author     Michael Bordash <michael@internetdj.com>
 */
class Kontxt_Public {

    private $option_name    = 'KONTXT';
    private $api_host       = 'http://api.kontxt.cloud/wp-json/kontxt/v1/analyze';
	private $api_host_only  = 'api.kontxt.cloud';
	private $api_host_uri   = '/wp-json/kontxt/v1/analyze';
	private $api_host_proto = 'http://';
	private $api_host_port  = 80;

	# protected string $api_host     = 'http://kontxt.com/wp-json/kontxt/v1/analyze';

	/**
	 * The ID of this plugin
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
		// $this->fp           = @pfsockopen($this->api_host_only, $this->api_host_port, $errno, $errstr );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kontxt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kontxt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

    }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kontxt_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kontxt_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


	}

	public function kontxt_capture_search( ) {

		// capture text input
		$searchQuery = get_search_query();

		if( isset( $searchQuery ) || $searchQuery !== '' ) {

				$services = [ 'intents', 'concepts', 'sentiment', 'keywords', 'emotion' ];
		        $this->kontxt_capture_user_event( $searchQuery, $services );

		}

	}


	/**
	 * @param $eventData
	 * @param $services
	 * @param bool $silent
	 *
	 * @return false|mixed|string
	 */
	public function kontxt_capture_user_event( $eventData, $services, $silent = true ) {

		//get and check API key exists, pass key along server side request
	    $apiKey             = get_option( $this->option_name . '_apikey' );
	    $apiUid             = get_option( $this->option_name . '_apiuid' );
	    $current_session    = $_COOKIE['kontxt_session'];
		$current_user       = wp_get_current_user();

        if ( !isset($apiKey) || $apiKey === '' ) {

            $response_array['status'] = "error";
            $response_array['message'] = "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first.";

        }

		if( !isset( $current_session ) ) {

			$current_session = $this->genKey();
			setcookie('kontxt_session', $current_session, strtotime( '+30 days' ) );

		}

		if( !isset( $requestId ) ) {
			$requestId = 'req_' . $this->genKey();
		}

        // get current user info, if no user, set anon id
        if( 0 == $current_user->ID ) {
	        $current_user_username = $current_session;
        } else {
	        $current_user_username = $current_user->user_login;
        }

        if ( isset( $eventData ) && $eventData !== '' ) {

        	$eventData = urlencode( sanitize_text_field( $eventData ) );
	        $requestId = sanitize_text_field( $requestId );

	        foreach ( $services as $service ) {

	        	$service   = sanitize_text_field( $service );

		        $requestBody = array(
			        'api_uid'                => $apiUid,
			        'api_key'                => $apiKey,
			        'kontxt_text_to_analyze' => $eventData,
			        'service'                => $service,
			        'request_id'             => $requestId,
			        'current_user_username'  => $current_user_username,
			        'current_session_id'     => $current_session,
			        'user_class'             => 'public',
			        'silent'                 => $silent
		        );

		        $args = array(
			        'timeout'   => '0.01',
		        	'body'      => $requestBody,
			        'headers'   => 'Content-type: application/x-www-form-urlencoded',
                    'blocking'  => false,
                    'method'    => 'PUT',
                    'sslverify' => false
		        );

//		        if( $this->fp ) {
//					error_log('posting via socket' . $this->fp );
//
//					$out =  "GET " . $this->api_host_uri . "?" . http_build_query( $requestBody ) . " HTTP/1.1\r\n";
//					$out .= "Host: " . $this->api_host_only . "\r\n";
//					$out .= "Content-type: application/x-www-form-urlencoded\r\n";
//
//					error_log( $out );
//
//			        @fwrite( $this->fp, $out );
//		        }

		        wp_remote_get( $this->api_host, $args );
	        }
        }
    }


    /**
     * Sanitize the text value before being saved to database
     *
     * @param  string $text $_POST value
     * @since  1.3.2
     * @return string           Sanitized value
     */
    public function kontxt_sanitize_text( $text ) {

        return sanitize_text_field( $text );

    }

	/**
	 * @return string
	 */
	public function genKey() {

		$api_key = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

		//error_log( "uniq id site key: " . $api_key);

		return $api_key;

	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __destruct( ) {

		// fclose($this->fp);

	}

}
