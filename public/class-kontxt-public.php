<?php

/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kontxt
 * @subpackage Kontxt/public
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kontxt-public.css', array(), $this->version, 'all' );

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

		$prodRecs       = get_option( $this->option_name . '_product_recs' );
		$contentRecs    = get_option( $this->option_name . '_content_recs' );

		$kontxt_ajax_info = array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'security'  => wp_create_nonce( 'kontxt-ajax-string' ),
			'action' => 'kontxt_send_event',
			'return_product_recs' => $prodRecs,
			'return_content_recs' => $contentRecs
		);

		wp_localize_script( $this->plugin_name, 'kontxtAjaxObject', $kontxt_ajax_info );
		wp_localize_script( $this->plugin_name, 'kontxtUserObject', $this->kontxt_capture_session() );

		wp_enqueue_script( $this->plugin_name );

	}

	/**
	 * @param $commentId
	 */
	public function kontxt_comment_post( $commentId ) {

		$kontxtCommentArr = [];

		// capture comment content
		if ( $commentId ) {

			$comment = get_comment( intval( $commentId ) );

			$comment_text   = sanitize_text_field( $comment->comment_content );
			$comment_product_id = $comment->comment_post_ID;
			if( function_exists('wc_get_product' ) ) {
				$comment_product_name = wc_get_product( $comment_product_id )->get_name();
			}
			$comment_rating = get_comment_meta( $commentId, 'rating', true);

			if ( ! empty( $comment_text ) ) {

				$kontxtCommentArr['comment_submitted'] = [
					'comment_text' => $comment_text,
					'comment_rating' => $comment_rating,
					'comment_product_id' => $comment_product_id,
					'comment_product_name' => $comment_product_name
				];

			}
		}

		// send directly to backend, don't bother with js async
		$this->kontxt_send_event( $kontxtCommentArr, 'public_event' );

	}

	/**
	 * @param $user_id
	 */
	public function kontxt_user_register( $user_id ) {

		if( $user_id ) {
			$kontxtUserRegArr['user_register'] = [
				'user_id' => $user_id
			];

			// send directly to backend, don't bother with js async
			$this->kontxt_send_event( $kontxtUserRegArr, 'public_event' );

		}
	}

	public function kontxt_search_capture( $where, $query ) {

		// capture text input actively if search optimization is enabled
		$optimizeSearch = get_option( $this->option_name . '_optimize_search' );

		if( $optimizeSearch === 'yes') {

			$searchQuery         = get_search_query();
			$kontxt_search_query = [];

			if ( !is_admin() && is_search() && $query->is_main_query() && isset( $searchQuery ) ) {

				global $wpdb;

				$kontxt_search_query['search_query'] = array(
					'search_query'  => $searchQuery
				);

				$kontxt_search_query['return_search_intent'] = true;

				// request intent prediction from our classifier
				$intentResults = $this->kontxt_send_event( $kontxt_search_query, 'public_event' );

				error_log( print_r( $intentResults, true ) );

				// Overwrite the existing WHERE clause.
				$where = '';

				// explode all search terms into array.
				$search_terms = explode( ' ', get_search_query() );
				$count_terms  = count( $search_terms );

				// Tables names.
				$type       = $wpdb->prefix . "posts.post_type";
				$status     = $wpdb->prefix . "posts.post_status";
				$title      = $wpdb->prefix . "posts.post_title";
				$content    = $wpdb->prefix . "posts.post_content";

				$where .= " AND ( ";
				$counter = 0;
				foreach ( $search_terms as $term ) {
					$counter++;
					$term = trim( $term );
					$where .= " ( $title LIKE '%$term%' ) OR ( $content LIKE '%$term%' ) ";
					if( $counter < $count_terms ) {
						$where .= " OR ";
					}
				}
				$where .= " ) ";

				// As WHERE clause is overwritten, you'll need to specify the post type, the status and/or anything else you need.
				// Post Types.
				$where .= " AND ($type IN ('post', 'page', 'product' )) ";
				// Post status.
				$where .= " AND ($status = 'publish') ";

				error_log( print_r( $query, true ));
			}

		}

		return $where;
	}

	public function kontxt_post_type( $query ) {

		// capture text input actively if search optimization is enabled
		$optimizeSearch = get_option( $this->option_name . '_optimize_search' );

		if( $optimizeSearch === 'yes') {

			if ( ! is_admin() && $query->is_main_query() ) {
				if ( $query->is_search ) {
					$query->set( 'post_type', array( 'post', 'page', 'product' ) );
				}
			}
		}
	}


	/**
	 * @param $data
	 */
	public function kontxt_contact_form_capture( $data ) {

		$kontxtContactFormArr = [];

		// capture contact us content
		if ( isset( $data['your-message'] ) ) {

			$kontxtContactFormArr['contact_form_submitted'] = [
				'contact_form_subject' => sanitize_text_field( $data['your-subject'] ),
				'contact_form_message' => sanitize_text_field( $data['your-message'] )
			];
		} else if( function_exists( 'rgar' ) ) {

			$kontxtContactFormArr['contact_form_submitted'] = [
				'contact_form_message' => sanitize_text_field( rgar( $data, '3' ) )
			];

		}

		// send directly to backend, don't bother with js async
		$this->kontxt_send_event( $kontxtContactFormArr, 'public_event' );

	}

	public function kontxt_cart_capture( ) {

		$kontxtCartArr = [];

		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {

			$cartData = WC()->cart->get_cart_contents();

			$cartDataArray[] = array();

			foreach( $cartData as $cart_item_key => $cart_item ) {

				$cartDataArray[] = array(
					'cart_product_id'   => $cart_item['product_id'],
					'cart_product_name' => wc_get_product($cart_item['product_id'])->get_name()
				);

			}
			$kontxtCartArr['cart_add'] = $cartDataArray;
		}

		// send directly to backend, don't bother with js async
		$this->kontxt_send_event( $kontxtCartArr, 'public_event' );

	}

	/**
	 * @param $order_id
	 */
	public function kontxt_order_post( $order_id ) {

		$orderCapture[] = [];

		if( $order_id ) {

			$orderProducts[] = null;

			$order = wc_get_order( $order_id );
			foreach( $order->get_items() as $item_id => $item ){

				$orderProducts['product_id']    = $item['product_id']; // Get the product ID
				$orderProducts['variation_id']  = $item['variation_id']; // Get the variation ID
				$orderProducts['product_name']  = $item['name']; // The product name
				$orderProducts['item_qty']      = $item['quantity']; // The quantity
				$orderProducts['line_subtotal'] = $item['line_subtotal'];  // The line subtotal
				$orderProducts['line_total']    = $item['line_total'];  // The line subtotal

			}

			$orderCapture['order_received'] = [
				'order_id'      => $order_id,
				'order_date'    => $order->get_date_created(),
				'order_total'   => $order->get_total(),
				'products'      => $orderProducts
			];

		}

		// send directly to backend, don't bother with js async
		$this->kontxt_send_event( $orderCapture, 'public_event' );

	}

	/**
	 * @param array $kontxt_user_session
	 *
	 * @return array
	 */
	public function kontxt_capture_session( $kontxt_user_session  = [] ) {

		// this captures various event data passively via passing back results to the DOM
		// for round trip ticket back to the local API

		$pageName       = null;
		$searchQuery    = get_search_query();

		// capture text input passively if search optimization is disabled
		$optimizeSearch = get_option( $this->option_name . '_optimize_search' );

		if( $optimizeSearch !== 'yes') {

			if ( $searchQuery ) {

				$kontxt_user_session['search_query'] = array(
					'search_query'  => $searchQuery
				);

				// statistically interesting to see which search queries returned no results
				if ( ! have_posts() ) {
					$kontxt_user_session['no_search_results'] = array(
						'search_query'  => $searchQuery
					);
				}
			}
		}

		// determine non-shopping page presence
		if( is_front_page() || is_home() ) {
			$kontxt_user_session['site_home'] = [
				'page_name'     => 'site home',
				'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''
			];
		} elseif( get_post_type() === 'post' ) {
			$kontxt_user_session['blog_post'] = [
				'title' => get_the_title(),
				'id'    => get_the_ID(),
				'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''
			];
		} elseif( get_post_type() === 'page' ) {
			$kontxt_user_session['site_page'] = [
				'title' => get_the_title(),
				'id'    => get_the_ID(),
				'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''
			];
		}

		// get commerce related major actions; check if not search query otherwise we'll get duplicate events
		if ( !$searchQuery && class_exists( 'WooCommerce', false )  ) {

			if( is_shop() ) {

				$kontxt_user_session['shop_page_home'] = [
					'page_name' => 'shop home',
					'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''
				];

			} elseif( isset( get_queried_object()->term_id) ) {

				$categoryId   = get_queried_object()->term_id;
				$categoryName = get_the_category_by_ID( get_queried_object( )->term_id);

				$categoryDataArray = array(

					'view_category_id'   => $categoryId,
					'view_category_name' => $categoryName,
					'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''

				);

				$kontxt_user_session['shop_page_category'] = $categoryDataArray;

			} else {

				// current product data
				if ( wc_get_product() ) {

					$productId   = wc_get_product()->get_id();
					$productName = wc_get_product()->get_name();

					$productDataArray = array(

						'view_product_id'   => $productId,
						'view_product_name' => $productName,
						'http_referrer' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : ''

					);
					$kontxt_user_session['shop_page_product'] = $productDataArray;
				}
			}

		}

		return $kontxt_user_session;

	}

	/**
	 * @param $eventData
	 * @param string $service
	 *
	 * @return false|mixed|string
	 */
	public function kontxt_send_event( $eventData, $service = 'public_event' ) {

		$returnContentRecs  = false;
		$returnProductRecs  = false;
		$returnSearchIntent = false;
		$silent             = true;
		$userClass          = 'public';
		$responseBody       = array();

		// grab variables from the POST

		//check to see if event data is passed to us via a function or if we have event data in the request object from a direct call
		if( isset( $_POST['eventData'] ) && $_POST['eventData'] !== '' && $_POST['eventData'] !== false ) {

			check_ajax_referer( 'kontxt-ajax-string', 'security', false );
			// each element of the following event is sanitized by the backend receiver
			// no need to do this here
			$eventData = json_decode( stripslashes( $_POST['eventData'] ) );

		}

		if( isset( $_POST['return_product_recs'] ) && $_POST['return_product_recs'] === 'yes' ) {

			$returnProductRecs = true;
			$silent = false;

		}

		if( isset( $_POST['return_content_recs'] ) && $_POST['return_content_recs'] === 'yes' ) {

			$returnContentRecs = true;
			$silent = false;

		}

		if( !is_object( $eventData ) ) {
			if( isset( $eventData['return_search_intent'] ) ) {

				$returnSearchIntent = true;
				$silent             = false;
			}
		}

		//get and check API key exists, pass key along server side request
	    $apiKey             = get_option( $this->option_name . '_apikey' );
	    $apiUid             = get_option( $this->option_name . '_apiuid' );
		$current_user       = wp_get_current_user();

        if ( !isset($apiKey) || $apiKey === '' ) {
            error_log( "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first." );
            return false;
        }

		if( !isset( $_COOKIE['kontxt_session'] ) ) {
			$current_session = $this->genKey();
			setcookie('kontxt_session', $current_session, strtotime( '+30 days' ), COOKIEPATH, COOKIE_DOMAIN);
		} else {
			$current_session = $_COOKIE['kontxt_session'];
		}

		if( !isset( $requestId ) ) {
			$requestId = 'req_' . $this->genKey();
		}

        // get current user info, if no user, set anon id
        if( 0 == $current_user->ID ) {
	        $current_user_username = $current_session;
        } else {
	        $current_user_username = hash( 'SHA256', $current_user->user_login );
        }

        if ( isset( $eventData ) && $eventData !== '' ) {

	        $requestId = sanitize_text_field( $requestId );

            $service   = sanitize_text_field( $service );

            //error_log( $current_user_username );

	        $requestBody = array(

		        'api_uid'                => $apiUid,
		        'api_key'                => $apiKey,
		        'kontxt_text_to_analyze' => [$eventData],
		        'service'                => $service,
		        'request_id'             => $requestId,
		        'current_user_username'  => $current_user_username,
		        'current_session_id'     => $current_session,
		        'user_class'             => $userClass,
		        'silent'                 => $silent,
		        'return_product_recs'    => $returnProductRecs,
		        'return_content_recs'    => $returnContentRecs,
		        'return_search_intent'   => $returnSearchIntent

	        );

	        $args = array(

	            'body'      => $requestBody,
		        'headers'   => 'Content-type: application/x-www-form-urlencoded',
                'method'    => 'GET',
                'sslverify' => false

	        );

            $response = wp_remote_request( $this->api_host, $args );

            if ( is_array( $response ) && ! is_wp_error( $response ) && $silent === false ) {

	            $recResults = json_decode( $response['body'] );

	            //error_log(print_r($recResults,true));

	            // look through results and check if we need to return recommendations inline
	            if( is_array( $recResults ) ) {

		            if ( $returnSearchIntent === true ) {

			            return json_encode( $recResults );

		            } elseif( $returnProductRecs === true || $returnContentRecs === true ) {

			            foreach ( $recResults as $items ) {

				            if ( $returnProductRecs === true ) {

					            $product = wc_get_product( $items->item_id );

					            $responseBody[] = array(

						            'item_id'    => $items->item_id,
						            'item_url'   => esc_url( $product->get_permalink() ),
						            'item_image' => $product->get_image( 'woocommerce_thumbnail' ),
						            'item_name'  => wp_kses_post( $product->get_name() ),
						            'item_price' => $product->get_price_html()

					            );

					            echo json_encode( $responseBody );

				            } else if ( $returnContentRecs === true ) {

					            $post = get_post( $items->item_id );

					            $responseBody[] = array(

						            'item_id'    => $items->item_id,
						            'item_url'   => get_permalink( $items->item_id ),
						            'item_image' => get_the_post_thumbnail( $items->item_id ),
						            'item_name'  => $post->post_title

					            );

					            echo json_encode( $responseBody );

				            }

			            }
		            } else {
		            	return null;
		            }
	            }
            }

        }

	}


	/**
	 *
	 */
	public function kontxt_generate_recs( ) {

		$prodRecs       = get_option( $this->option_name . '_product_recs' );
		$contentRecs    = get_option( $this->option_name . '_content_recs' );

		if( !is_admin() && ( $prodRecs === 'yes' || $contentRecs === 'yes' ) ) {

			include_once('partials/kontxt-recommendations.php');

		}

	}

	/**
	 * @return string
	 */
	public function genKey() {

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __destruct( ) {


	}

}
