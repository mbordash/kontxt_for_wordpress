<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kontxt
 * @subpackage Kontxt/admin
 * @author     Michael Bordash <mbordash@realnetworks.com>
 */
class Kontxt_Admin {

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
	 */
	public function __construct( $plugin_name, $version, $option_name, $api_host )
	{

		$this->plugin_name          = $plugin_name;
		$this->version              = $version;
		$this->option_name          = $option_name;
		$this->api_host             = $api_host;
		$this->analyze_api_path     = 'analyze';
		$this->analytics_api_path   = 'analytics';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kontxt-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );

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

        wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kontxt-admin-functions.js', array( 'jquery', 'wp-rich-text', 'wp-element', 'wp-rich-text' ), $this->version, true );


		$kontxt_local_arr = array(
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'security'  => wp_create_nonce( 'kontxt-ajax-string' ),
            'apikey'    => get_option( $this->option_name . '_apikey' ),
	        'apiuid'    => get_option( $this->option_name . '_apiuid' )
        );

        wp_localize_script( $this->plugin_name, 'kontxtAjaxObject', $kontxt_local_arr );

		wp_enqueue_script( $this->plugin_name );

		wp_enqueue_script( $this->plugin_name . '-plotly', plugin_dir_url( __FILE__ ) . 'js/plotly.min.js', null, $this->version, true );
        wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// CEO panel for Gutenberg editor

	}

	/**
	 * Register Kontxt SEO panel for Gutenberg block editor
	 */
	public function register_kontxt_seo_block() {

		wp_register_script(
			$this->plugin_name . '_sidebar',
			plugins_url( 'js/kontxt-admin-panel/build/index.js', __FILE__ ),
			array( 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element', 'wp-components', 'wp-compose' )
		);

		wp_enqueue_script( $this->plugin_name . '_sidebar' );

	}


	/**
	 * Handle retrieval of analytics results
     *
	 */
	public function kontxt_analyze_results()
    {

	    if (!current_user_can('manage_options')) {
		    wp_die('You are not allowed to be on this page.');
	    }

	    check_ajax_referer( 'kontxt-ajax-string', 'security', false );

	    if ( isset( $_POST['dimension'] ) && $_POST['dimension'] !== '' ) {

	        if( isset( $_POST['filter'] ) && $_POST['filter'] !== '' && $_POST['filter'] !== false ) {
	        	$filter =  $_POST['filter'];
	        } else {
	        	$filter = null;
	        }

	        // echo already sanity checked and encoded results
		    echo $this->kontxt_get_results( $_POST['dimension'], $_POST['from_date'], $_POST['to_date'], $filter ) ;

	    }

	    exit;
    }

	/**
	 * @param $dimension
	 * @param $from_date
	 * @param $to_date
	 * @param $filter
	 *
	 * @return bool|false|string
	 */
	public function kontxt_get_results( $dimension, $from_date, $to_date, $filter ) {

		//get and check API key exists, pass key along server side request
		$apiKey = get_option( $this->option_name . '_apikey' );
		$apiUid = get_option( $this->option_name . '_apiuid' );
		$current_session    = isset( $_COOKIE['kontxt_session'] ) ? sanitize_text_field( $_COOKIE['kontxt_session']) : '';
		$current_user       = wp_get_current_user();


		if ( !isset($apiKey) || $apiKey === '' ) {

			$response_array['status'] = "error";
			$response_array['message'] = "Your API Key for KONTXT is not set. Please go to Settings > KONTXT to make sure you have a key first.";

			return json_encode($response_array);

		}

		if ( isset( $dimension ) && $dimension !== '' ) {

			$dimension = sanitize_text_field( $dimension );
			$service = 'events';

			// get current user info, if no user, set as session

			if( 0 === $current_user->ID ) {
				$current_user_username = $current_session;
			} else {
				$current_user_username = $current_user->user_login;
			}

			if( !isset( $current_session ) ) {
				$current_session = 'anon_' . $this->genKey();
				setcookie('kontxt_session', $current_session, strtotime( '+30 days' ) );
			}

			$requestBody = array (
                'api_uid'                   => $apiUid,
                'api_key'                   => $apiKey,
                'event_type'                => $dimension,
                'current_user_username'     => $current_user_username,
                'current_session_id'        => $current_session,
                'user_class'                => 'admin'
            );

			if( $filter ) {
				$filter                = sanitize_text_Field( $filter );
				$requestBody['filter'] = $filter;
			}

			if( $from_date ) {
				$from_date                = sanitize_text_Field( $from_date );
				$requestBody['from_date'] = $from_date;
			}

			if( $to_date ) {
				$to_date                = sanitize_text_Field( $to_date );
				$requestBody['to_date'] = $to_date;
			}

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded',
                'timeout'   => 45
			);

			$response = wp_remote_get($this->api_host . '/' . $this->analytics_api_path . '/' . $service, $opts);

			if( !is_wp_error( $response ) ) {

				return sanitize_text_field( $response['body'] );

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Error. Could not get event data. Code received: " . $response['response']['code'];

				return wp_json_encode( $response_array );

			}
		}

		return true;

	}

    /**
     * Handle ajax request for text processing and display
     *
     * @since  1.0.0
     */
    public function kontxt_process_text()
    {

    	$requestId = null;

        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to be on this page.');
        }

        check_ajax_referer( 'kontxt-ajax-string', 'security', false );

        if ( isset( $_POST['kontxt_text_to_analyze'] ) && $_POST['kontxt_text_to_analyze'] !== '' ) {

	        if ( isset( $_POST['request_id'] ) ) {
		        $requestId = (string)  $_POST['request_id'];
	        }

            // echo already sanity checked json
            echo $this->kontxt_cognitive(
	            sanitize_text_field( $_POST['kontxt_text_to_analyze'] ),
	            sanitize_text_field( $_POST['service'] ),
	            sanitize_text_field( $requestId )
            );
        }

	    exit;
    }

    public function kontxt_cognitive( $textToAnalyze, $service, $requestId, $silent = false )
    {

        //get and check API key exists, pass key along server side request
	    $apiKey = get_option( $this->option_name . '_apikey' );
	    $apiUid = get_option( $this->option_name . '_apiuid' );
	    $current_session    = isset( $_COOKIE['kontxt_session'] ) ? sanitize_text_field( $_COOKIE['kontxt_session']) : '';
	    $current_user       = wp_get_current_user();

        if ( !isset($apiKey) || $apiKey === '' ) {

            $response_array['status'] = "error";
            $response_array['message'] = "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first.";

            return json_encode($response_array);

        }

	    if( 0 === $current_user->ID ) {
		    $current_user_username = $current_session;
	    } else {
		    $current_user_username = $current_user->user_login;
	    }

	    if( !isset( $current_session ) ) {
		    $current_session = 'anon_' . $this->genKey();
		    setcookie('kontxt_session', $current_session, strtotime( '+30 days' ) );
	    }

	    if ( isset( $textToAnalyze ) && $textToAnalyze !== '' ) {

            $textToAnalyze  = urlencode( sanitize_text_field( $textToAnalyze ) );
            $service        = sanitize_text_field( $service );
            $requestId      = sanitize_text_field( $requestId );
            $silent         = sanitize_text_field( $silent );

            $requestBody = array(
                    'api_uid'                   => $apiUid,
                    'api_key'                   => $apiKey,
                    'kontxt_text_to_analyze'    => $textToAnalyze,
                    'request_id'                => $requestId,
                    'current_user_username'     => $current_user_username,
                    'current_session_id'        => $current_session,
                    'user_class'                => 'admin',
                    'silent'                    => $silent
            );

            $opts = array(
                'body'      => $requestBody,
                'headers'   => 'Content-type: application/x-www-form-urlencoded'
            );

            $response = wp_remote_get($this->api_host . '/' . $this->analyze_api_path . '/' . $service , $opts);

            if( $response['response']['code'] === 200 ) {

                return sanitize_text_field( $response['body'] );

            } else {

                $response_array['status'] = "error";
                $response_array['message'] = "Plugin Error. Something went wrong with this request. Code received: " . $response['response']['code'];

                return wp_json_encode($response_array);

            }
        }

	    return true;
    }

	/**
	 * Add the KONTXT analyze page under the Tools submenu
	 *
	 * @since  1.0.0
	 */
	public function add_management_page()
	{

		$allowed_html = get_option( 'kontxt_allowable_html' );

		$this->plugin_screen_hook_suffix = add_menu_page(
			wp_kses(__( 'KONTXT', 'kontxt' ), $allowed_html ),
			wp_kses(__( 'KONTXT', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_analyze_page' ),
			'dashicons-analytics',
			30
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'Dashboard', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Dashboard', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_analyze_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Journey Analytics', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Journey', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_journey",
			array( $this, 'display_journey_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Sentiment', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Sentiment', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_sentiment",
			array( $this, 'display_sentiment_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Emotion', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Emotion', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_emotion",
			array( $this, 'display_emotion_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Intents', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Intents', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_intents",
			array( $this, 'display_intents_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Keywords', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Keywords', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_keywords",
			array( $this, 'display_keywords_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			wp_kses(__( 'KONTXT Settings', 'kontxt' ), $allowed_html ),
            wp_kses(__( 'Settings', 'kontxt' ), $allowed_html ),
			'manage_options',
			$this->plugin_name . "_settings",
			array( $this, 'display_options_page' )
		);

	}

	/**
	 * Render the journey page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_journey_page()
	{
		include_once 'partials/kontxt-journey-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_sentiment_page()
	{
		include_once 'partials/kontxt-sentiment-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_emotion_page()
	{
		include_once 'partials/kontxt-emotion-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_intents_page()
	{
		include_once 'partials/kontxt-intents-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_keywords_page()
	{
		include_once 'partials/kontxt-keywords-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_analyze_page()
	{
		include_once 'partials/kontxt-analyze-display.php';
	}

	/**
	 * Render the analyze page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_experiment_page()
	{
		include_once 'partials/kontxt-experiment-display.php';
	}

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page()
    {
        include_once 'partials/kontxt-admin-display.php';
    }

    /**
     * Register all related settings of this plugin
     *
     * @since  1.0.0
     */
    public function register_setting()
    {

	    $allowed_html = get_option( 'kontxt_allowable_html' );

        add_settings_section(
            $this->option_name . '_general',
	        wp_kses(__( '', 'kontxt' ), $allowed_html ),
            array( $this, $this->option_name . '_general_cb' ),
            $this->plugin_name
        );

	    add_settings_field(
		    $this->option_name . '_optin',
		    wp_kses(__( 'Opt-in to site traffic analysis from KONTXT?', 'kontxt' ), $allowed_html ),
		    array( $this, $this->option_name . '_optin_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_optin' )
	    );

	    add_settings_field(
		    $this->option_name . '_apiuid',
		    wp_kses(__( 'API User ID', 'kontxt' ), $allowed_html ),
		    array( $this, $this->option_name . '_apiuid_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array('label_for' => $this->option_name . '_apiuid')
	    );

        add_settings_field(
            $this->option_name . '_apikey',
	        wp_kses(__( 'API Key', 'kontxt' ), $allowed_html ),
            array( $this, $this->option_name . '_apikey_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_apikey' )
        );

	    add_settings_field(
		    $this->option_name . '_email',
		    wp_kses(__( 'Contact Email', 'kontxt' ), $allowed_html ),
		    array( $this, $this->option_name . '_email_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_email' )
	    );

	    add_settings_field(
		    $this->option_name . '_product_recs',
		    wp_kses(__( 'Activate recommendations block?', 'kontxt' ), $allowed_html ),
		    array( $this, $this->option_name . '_recs_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_product_recs' )
	    );

	    add_settings_field(
		    $this->option_name . '_optimize_search',
		    wp_kses(__( 'Optimize search results? (English only)', 'kontxt' ), $allowed_html ),
		    array( $this, $this->option_name . '_optimize_search_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_optimize_search' )
	    );

	    register_setting( $this->plugin_name, $this->option_name . '_apiuid', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_apikey', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_email', array( $this, $this->option_name . '_sanitize_email' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_optin', array( $this, $this->option_name . '_sanitize_option' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_product_recs', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_content_recs', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_optimize_search', array( $this, $this->option_name . '_sanitize_text' ) );


    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function kontxt_general_cb()
    {

    }

	/**
	 * Render the radio input field for global option
	 *
	 * @since  1.0.12
	 */
	public function kontxt_optimize_search_cb() {

		$optimizeSearch = get_option( $this->option_name . '_optimize_search' );
		$allowed_html = get_option( 'kontxt_allowable_html' );

		echo wp_kses( __('<p>If enabled, KONTXT machine learning will attempt to optimize search results using semantic intent detection & lemmas.</p>', 'kontxt' ), $allowed_html );

        ?>

        <fieldset>
            <label>
                <input type="radio" name="<?php echo __( $this->option_name . '_optimize_search', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_optimize_search', 'kontxt' );  ?>" value="yes" <?php checked( $optimizeSearch, 'yes' ); ?>>
				<?php _e( 'Yes', 'kontxt' ); ?>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo __( $this->option_name . '_optimize_search', 'kontxt' ); ?>" value="no" <?php checked( $optimizeSearch, 'no' ); ?>>
				<?php _e( 'No', 'kontxt' ); ?>
            </label>
        </fieldset>

		<?php
	}

	/**
	 * Render the radio input field for global option
	 *
	 * @since  1.0.0
	 */
	public function kontxt_optin_cb() {

		$optin = get_option( $this->option_name . '_optin' );

		?>

        <fieldset>
            <label>
                <input type="radio" name="<?php echo __( $this->option_name . '_optin', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_optin', 'kontxt' ); ?>" value="yes" <?php checked( $optin, 'yes' ); ?>>
				<?php _e( 'Yes', 'kontxt' ); ?>
            </label>
            <br />
            <label>
                <input type="radio" name="<?php echo __( $this->option_name . '_optin', 'kontxt' ); ?>" value="no" <?php checked( $optin, 'no' ); ?>>
				<?php _e( 'No', 'kontxt' ); ?>
            </label>
        </fieldset>

		<?php
	}

	/**
	 * Render the radio input field for recs option
	 *
	 * @since  1.0.0
	 */
	public function kontxt_recs_cb() {

		$prodRecs       = get_option( $this->option_name . '_product_recs' );
		$contentRecs    = get_option( $this->option_name . '_content_recs' );
		$allowed_html   = get_option( 'kontxt_allowable_html' );

		?>

        <p>If enabled, KONTXT machine learning will show recommended articles or products from your site.</p>

		<fieldset>
			<label>
				<input type="checkbox" name="<?php echo __( $this->option_name . '_product_recs', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_product_recs', 'kontxt' ); ?>" value="yes" <?php checked( $prodRecs, 'yes' ); ?>>
				<?php _e( 'Product recommendations (for WooCommerce stores)', 'kontxt' ); ?>
			</label>
			<br />
			<label>
				<input type="checkbox" name="<?php echo __( $this->option_name . '_content_recs', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_content_recs', 'kontxt' ); ?>" value="yes" <?php checked( $contentRecs, 'yes' ); ?>>
				<?php _e( 'Content recommendations (for WordPress blog articles)', 'kontxt' ); ?>
			</label>

		</fieldset>

		<?php

		echo wp_kses( __('<p>Depending on the volume of your traffic, it may take a few hours or days before KONTXT machine learning determines recommendations for your visitors.</p>', 'kontxt' ), $allowed_html );

	}

	/**
	 * Render the text input field for email option
	 *
	 * @since  1.3.2
	 */
	public function kontxt_email_cb()
	{

		$email          = get_option( $this->option_name . '_email' );
		$allowed_html   = get_option( 'kontxt_allowable_html' );

		?>

        <fieldset>
            <label>
                <input class="regular-text" type="text" name="<?php echo __( $this->option_name . '_email', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_email', 'kontxt' ); ?>" value="<?php echo wp_kses( __( $email, 'kontxt' ), $allowed_html ) ?>">
            </label>
        </fieldset>

		<?php
	}


    /**
     * Render the text input field for apikey option
     *
     * @since  1.3.2
     */
    public function kontxt_apikey_cb()
    {

        $apikey = get_option( $this->option_name . '_apikey' );
	    $allowed_html   = get_option( 'kontxt_allowable_html' );

        ?>

        <fieldset>
            <label>
                <input class="regular-text" type="text" name="<?php echo __( $this->option_name . '_apikey', 'kontxt' ); ?>" id="<?php echo __( $this->option_name . '_apikey', 'kontxt' ); ?>" value="<?php echo wp_kses( __( $apikey, 'kontxt' ), $allowed_html ) ?>" readonly>
            </label>
        </fieldset>

        <?php
    }


	/**
	 * Render the text input field for apiuid
	 *
	 * @since  1.3.2
	 */
	public function kontxt_apiuid_cb()
	{

		$apiuid = get_option( $this->option_name . '_apiuid' );
		$allowed_html   = get_option( 'kontxt_allowable_html' );

		?>

        <fieldset>
            <label>
                <input class="regular-text" type="text" name="<?php echo __( $this->option_name . '_apiuid', 'kontxt' );  ?>" id="<?php echo __( $this->option_name . '_apiuid', 'kontxt' ); ?>" value="<?php echo wp_kses( __( $apiuid, 'kontxt' ), $allowed_html ) ?>" readonly>
            </label>
        </fieldset>

		<?php
	}


    /**
     * Sanitize the text value before being saved to database
     * TODO: replace with wordpress sanitize option function
     *
     * @param  string $text $_POST value
     * @since  1.0.0
     * @return string           Sanitized value
     */
    public function kontxt_sanitize_option( $text )
    {
        if ( in_array( $text, array( 'yes', 'no' ), true ) ) {
            return $text;
        }
    }

    /**
     * Sanitize the text value before being saved to database
     *
     * @param  string $text $_POST value
     * @since  1.3.2
     * @return string           Sanitized value
     */
    public function kontxt_sanitize_text( $text )
    {

        return sanitize_text_field( $text );

    }

	/**
	 * Sanitize the email value before being saved to database
	 *
	 * @param  string $text $_POST value
	 * @since  1.3.2
	 * @return string           Sanitized value
	 */
	public function kontxt_sanitize_email( $text )
	{

		return sanitize_email( $text );

	}


	/**
	 * @return string
	 */
	public function genKey() {

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

	}

}
