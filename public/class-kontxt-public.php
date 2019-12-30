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

	private $plugin_name;
	private $version;
	private $option_name;
	private $api_host;

	/**
	 * Kontxt_Public constructor.
	 * Kontxt_Public construct
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $option_name
	 * @param $api_host
	 */
	public function __construct( $plugin_name, $version, $option_name, $api_host )
	{

		$this->plugin_name      = $plugin_name;
		$this->version          = $version;
		$this->option_name      = $option_name;
		$this->api_host         = $api_host;

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
		global $comment, $wp_query, $category;

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

		$kontxt_ajax_info = array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'security'  => wp_create_nonce( 'kontxt-ajax-string' ),
			'action' => 'kontxt_send_event'
		);

		wp_localize_script( $this->plugin_name, 'kontxtAjaxObject', $kontxt_ajax_info );
		wp_localize_script( $this->plugin_name, 'kontxtUserObject', $this->kontxt_capture_session() );

		wp_enqueue_script( $this->plugin_name);

	}

	public function kontxt_comment_post( $commentId ) {

		$kontxt_comment_arr = [];

		// capture comment content
		if ( $commentId ) {

			$comment = get_comment( intval( $commentId ) );

			$comment_text   = $comment->comment_content;
			$comment_product_id = $comment->comment_post_ID;
			$comment_product_name = wc_get_product( $comment_product_id )->get_name();
			$comment_rating = get_comment_meta( $commentId, 'rating', true);

			if ( ! empty( $comment_text ) ) {

				$kontxt_comment_arr['comment_text'] = $comment_text;
				$kontxt_comment_arr['comment_rating'] = $comment_rating;
				$kontxt_comment_arr['comment_product_id'] = $comment_product_id;
				$kontxt_comment_arr['comment_product_name'] = $comment_product_name;

			}
		}

		// send directly to backend, don't bother with js async
		$this->kontxt_send_event( $kontxt_comment_arr, 'public_event', true );

	}

	public function kontxt_capture_session( $kontxt_user_session  = [] ) {
		global $wp_query, $category;

		$kontxt_user_session = [];

		// capture text input
		$searchQuery = get_search_query();
		if( $searchQuery ) {
			$kontxt_user_session['search_query'] =  $searchQuery;

			// statistically interesting to see which search queries returned not results
			if ( !have_posts() ) {
				$kontxt_user_session['no_results'] =  true;
			}
		}

		// current blog category or page name
		if( is_category() ) {
			$kontxt_user_session['blog_page'] = $category;
		} else {
			$pageName = $wp_query->queried_object->post_name;
			if ( $pageName ) {
				$kontxt_user_session['blog_page'] = $pageName;
			} elseif( is_front_page() || is_home() ) {
				$kontxt_user_session['blog_page'] = 'Site home';
			}
		}


		// get commerce related major actions
		if ( class_exists( 'WooCommerce', false )  ) {

			// override page as shop home
			if ( $searchQuery ) {

				$kontxt_user_session['shop_page'] = "Search results";

			} elseif( is_shop() ) {

				$kontxt_user_session['shop_page'] = "Shop home";

			} elseif( get_queried_object()->term_id ) {
				$categoryId   = get_queried_object()->term_id;
				$categoryName = get_the_category_by_ID( get_queried_object( )->term_id);

				$categoryDataArray = array(

					'view_category_id'   => $categoryId,
					'view_category_name' => $categoryName

				);

				$kontxt_user_session['shop_page'] = "category";
				$kontxt_user_session['category_data'] = $categoryDataArray;

			} else {

				// current product data
				if ( wc_get_product() ) {

					$productId   = wc_get_product()->get_id();
					$productName = wc_get_product()->get_name();

					$productDataArray = array(

						'view_product_id'   => $productId,
						'view_product_name' => $productName

					);
					$kontxt_user_session['shop_page'] = 'product';
					$kontxt_user_session['product_data'] = $productDataArray;
				}
			}

			// current cart data
			if( is_object( WC()->cart ) ) {

				$cartData = WC()->cart->get_cart_contents();

				$cartDataArray[] = array();

				foreach( $cartData as $cart_item_key => $cart_item ) {

					$cartDataArray[] = array(
						'cart_product_id'   => $cart_item['product_id'],
						'cart_product_name' => wc_get_product($cart_item['product_id'])->get_name()
					);

				}
				$kontxt_user_session['cart_data'] = $cartDataArray;
			}

			// let's get the current user ID and inspect completed orders
			$currentUserId = get_current_user_id();
			if( $currentUserId ) {

				$customerOrdersArray = wc_get_orders( array(
					'meta_key' => '_customer_user',
					'meta_value' => $currentUserId,
					'post_status' => [ 'wc-completed' ],
					'numberposts' => -1
				) );


				if ($customerOrdersArray ) {

					$orderCapture[] = array();

					foreach( $customerOrdersArray as $orderKey => $orderItem ) {

						$dateObj = get_object_vars( $orderItem->date_created );

						$order = wc_get_order( $orderItem->id );
						$orderItems = $order->get_items();

						$orderLines = array();

						foreach ( $orderItems as $item ) {
							$orderLines[] = array(
								'product_name'          => $item->get_name(),
								'product_id'            => $item->get_product_id(),
								'product_variation_id'  => $item->get_variation_id()
							);
						}

						$orderCapture[] = array(
							'order_id'          => $orderItem->id,
							'order_date'        => $dateObj['date'],
							'order_line_items'  => $orderLines
						);

					}

					$kontxt_user_session['completed_orders'] = $orderCapture;
				}
			}

		}

		// $this->kontxt_send_event( $kontxt_user_session, 'public_event', true );

		return $kontxt_user_session;

	}

	/**
	 * @param $eventData
	 * @param string $service
	 * @param bool $silent
	 *
	 * @return false|mixed|string
	 */
	public function kontxt_send_event( $eventData, $service = 'public_event', $silent = true ) {

		if( $_POST['eventData'] ) {
			check_ajax_referer( 'kontxt-ajax-string', 'security', false );
			$eventData = json_decode( html_entity_decode( stripslashes( $_POST['eventData'] ) ) );
		}

		//get and check API key exists, pass key along server side request
	    $apiKey             = get_option( $this->option_name . '_apikey' );
	    $apiUid             = get_option( $this->option_name . '_apiuid' );
	    $current_session    = $_COOKIE['kontxt_session'];
		$current_user       = wp_get_current_user();

        if ( !isset($apiKey) || $apiKey === '' ) {
            error_log( "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first." );
            return;
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

	        $requestId = sanitize_text_field( $requestId );

	        	$service   = sanitize_text_field( $service );

		        $requestBody = array(
			        'api_uid'                => $apiUid,
			        'api_key'                => $apiKey,
			        'kontxt_text_to_analyze' => [$eventData],
			        'service'                => $service,
			        'request_id'             => $requestId,
			        'current_user_username'  => hash('SHA256', $current_user_username),
			        'current_session_id'     => $current_session,
			        'user_class'             => 'public',
			        'silent'                 => $silent
		        );

		        $args = array(
			        'timeout'   => '1',
		        	'body'      => $requestBody,
			        'headers'   => 'Content-type: application/x-www-form-urlencoded',
                    'blocking'  => false,
                    'method'    => 'GET',
                    'sslverify' => false
		        );

		        wp_remote_request( $this->api_host, $args );

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
