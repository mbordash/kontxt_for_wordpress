<?php

/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kontxt
 * @subpackage Kontxt/admin
 * @author     Michael Bordash <mbordash@realnetworks.com>
 */
class Kontxt_Public {

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
	public function enqueue_scripts()
	{

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

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kontxt-public-functions.js', array( 'jquery', 'wp-rich-text', 'wp-element', 'wp-rich-text' ), $this->version, true );

		$kontxt_local_arr = array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'security'      => wp_create_nonce( 'kontxt-ajax-string' ),
			'apikey'        => get_option( $this->option_name . '_apikey' ),
			'apiuid'        => get_option( $this->option_name . '_apiuid' )
		);

		// capture text input
		$searchQuery = get_search_query();
		if( $searchQuery ) {
			$kontxt_local_arr['search_query'] =  $searchQuery;

			// statistically interesting to see which search queries returned not results
			if ( !have_posts() ) {
				$kontxt_local_arr['no_results'] =  true;
			}
		}

		// current post type
		$postType = get_post_type();
		if( $postType ) {
			$kontxt_local_arr['post_type'] =  $postType;
		}

		if ( class_exists( 'woocommerce' ) ) {

			// current product data
			if( wc_get_product() ) {

				$productId = wc_get_product()->get_id();
				$productName = wc_get_product()->get_name();

				$productDataArray = array(

					'product_id'    => $productId,
					'product_name'  => $productName

				);
				$kontxt_local_arr['product_data'] = $productDataArray;
			}

			// current cart data
			if( WC()->cart->get_cart_contents_count() >= 1 ) {

				$cartData = WC()->cart->get_cart_contents();


				foreach ( $cartData as $cart_item_key => $cart_item ) {

					$cartDataArray = array(
						'cart_product_id'   => $cart_item['product_id'],
						'cart_product_name' => wc_get_product($cart_item['product_id'])->get_name()
					);

				}
					$kontxt_local_arr['cart_data'] = $cartDataArray;
			}

			// current open order data
			//$orderData = wc_get_orders();
			//$kontxt_local_arr['order_data'] = $orderData;

		}

		wp_localize_script( $this->plugin_name, 'kontxtAjaxObject', $kontxt_local_arr );

		wp_enqueue_script( $this->plugin_name);



	}

	/**
	 * @param $eventData
	 * @param $services
	 * @param bool $silent
	 *
	 * @return false|mixed|string
	 */
	public function kontxt_send_event( $eventData, $services, $silent = true ) {

		check_ajax_referer( 'kontxt-ajax-string', 'security', false );

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
