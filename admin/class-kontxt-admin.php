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

		wp_enqueue_script( $this->plugin_name);

		wp_enqueue_script( $this->plugin_name . '-plotly', plugin_dir_url( __FILE__ ) . 'js/plotly.min.js', null, $this->version, true );
        wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

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

		    //header('Content-type: application/json');
		    echo $this->kontxt_get_results( $_POST['dimension'], $_POST['from_date'], $_POST['to_date'], $_POST['filter'] );

	    }

	    exit;
    }


	public function kontxt_get_results( $dimension, $from_date, $to_date, $filter = '' ) {

		//get and check API key exists, pass key along server side request
		$apiKey = get_option( $this->option_name . '_apikey' );
		$apiUid = get_option( $this->option_name . '_apiuid' );
		$current_session    = $_COOKIE['kontxt_anon_session'];
		$current_user       = wp_get_current_user();


		if ( !isset($apiKey) || $apiKey === '' ) {

			$response_array['status'] = "error";
			$response_array['message'] = "Your API Key for KONTXT is not set. Please go to Settings > KONTXT to make sure you have a key first.";

			return json_encode($response_array);

		}

		if ( isset( $dimension ) && $dimension !== '' ) {

			$dimension = sanitize_text_field( $dimension );

			// get current user info, if no user, set as session

			if( 0 == $current_user->ID ) {
				$current_user_username = $current_session;
			} else {
				$current_user_username = $current_user->user_login;
			}

			if( !isset( $current_session ) ) {
				$current_session = 'anon_' . $this->genKey();
				setcookie('kontxt_anon_session', $current_session, strtotime( '+30 days' ) );
			}

			$requestBody = array (
                'api_uid'                   => $apiUid,
                'api_key'                   => $apiKey,
                'service'                   => 'events',
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
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			$response = wp_remote_get($this->api_host, $opts);

			if( $response['response']['code'] === 200 ) {

				return $response['body'];

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Error. Could not get event data. Code received: " . $response['response']['code'];

				return json_encode($response_array);

			}
		}

	}


    /**
     * Handle ajax request for text processing and display
     *
     * @since  1.0.0
     */
    public function kontxt_process_text()
    {

        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to be on this page.');
        }

        check_ajax_referer( 'kontxt-ajax-string', 'security', false );

        if ( isset( $_POST['kontxt_text_to_analyze'] ) && $_POST['kontxt_text_to_analyze'] !== '' ) {

            //header('Content-type: application/json');
            echo $this->kontxt_cognitive( $_POST['kontxt_text_to_analyze'], $_POST['service'], $_POST['request_id'] );

        }

        exit;
    }

    public function kontxt_cognitive( $textToAnalyze, $service, $requestId, $silent = false )
    {

	    error_log( "request id: " . $requestId );
        //get and check API key exists, pass key along server side request
	    $apiKey = get_option( $this->option_name . '_apikey' );
	    $apiUid = get_option( $this->option_name . '_apiuid' );
	    $current_session    = $_COOKIE['kontxt_anon_session'];
	    $current_user       = wp_get_current_user();

        if ( !isset($apiKey) || $apiKey === '' ) {

            $response_array['status'] = "error";
            $response_array['message'] = "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first.";

            return json_encode($response_array);

        }

	    if( 0 == $current_user->ID ) {
		    $current_user_username = $current_session;
	    } else {
		    $current_user_username = $current_user->user_login;
	    }

	    if( !isset( $current_session ) ) {
		    $current_session = 'anon_' . $this->genKey();
		    setcookie('kontxt_anon_session', $current_session, strtotime( '+30 days' ) );
	    }

	    if ( isset( $textToAnalyze ) && $textToAnalyze !== '' ) {

            $textToAnalyze  = urlencode( sanitize_text_field( $textToAnalyze ) );
            $service        = sanitize_text_field( $service );
            $requestId      = sanitize_text_field( $requestId );

            $requestBody = array(
                    'api_uid'                   => $apiUid,
                    'api_key'                   => $apiKey,
                    'kontxt_text_to_analyze'    => $textToAnalyze,
                    'service'                   => $service,
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

            $response = wp_remote_get($this->api_host, $opts);

            if( $response['response']['code'] === 200 ) {

                return $response['body'];

                // error_log($response['body']);
                // error_log( print_r($_POST,true) );

            } else {

	            // error_log("Non-200 response");
	            // error_log( print_r($response, true) );
	            // error_log( print_r($_POST,true) );

                $response_array['status'] = "error";
                $response_array['message'] = "Plugin Error. Something went wrong with this request. Code received: " . $response['response']['code'];

                return json_encode($response_array);

            }
        }

    }

	/**
	 * Add the KONTXT analyze page under the Tools submenu
	 *
	 * @since  1.0.0
	 */
	public function add_management_page()
	{


		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'KONTXT', 'kontxt' ),
			__( 'KONTXT', 'kontxt' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_analyze_page' ),
			'dashicons-analytics',
			30
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'Dashboard', 'kontxt' ),
			__( 'Dashboard', 'kontxt' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_analyze_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Sentiment', 'kontxt' ),
			__( 'Sentiment', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_sentiment",
			array( $this, 'display_sentiment_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Emotion', 'kontxt' ),
			__( 'Emotion', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_emotion",
			array( $this, 'display_emotion_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Intents', 'kontxt' ),
			__( 'Intents', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_intents",
			array( $this, 'display_intents_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Keywords', 'kontxt' ),
			__( 'Keywords', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_keywords",
			array( $this, 'display_keywords_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Experiment', 'kontxt' ),
			__( 'Experiment', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_experiment",
			array( $this, 'display_experiment_page' )
		);

		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_name,
			__( 'KONTXT Settings', 'kontxt' ),
			__( 'Settings', 'kontxt' ),
			'manage_options',
			$this->plugin_name . "_settings",
			array( $this, 'display_options_page' )
		);

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
	public function display_concepts_page()
	{
		include_once 'partials/kontxt-concepts-display.php';
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

        add_settings_section(
            $this->option_name . '_general',
            __( 'General', 'kontxt' ),
            array( $this, $this->option_name . '_general_cb' ),
            $this->plugin_name
        );

	    add_settings_field(
		    $this->option_name . '_apiuid',
		    __( 'API User ID', 'kontxt' ),
		    array( $this, $this->option_name . '_apiuid_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_apiuid' )
	    );

        add_settings_field(
            $this->option_name . '_apikey',
            __( 'API Key', 'kontxt' ),
            array( $this, $this->option_name . '_apikey_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_apikey' )
        );

	    add_settings_field(
		    $this->option_name . '_email',
		    __( 'Contact Email', 'kontxt' ),
		    array( $this, $this->option_name . '_email_cb' ),
		    $this->plugin_name,
		    $this->option_name . '_general',
		    array( 'label_for' => $this->option_name . '_email' )
	    );


	    register_setting( $this->plugin_name, $this->option_name . '_apiuid', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_apikey', array( $this, $this->option_name . '_sanitize_text' ) );
	    register_setting( $this->plugin_name, $this->option_name . '_email', array( $this, $this->option_name . '_sanitize_text' ) );

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
	 * Render the text input field for email option
	 *
	 * @since  1.3.2
	 */
	public function kontxt_email_cb()
	{

		$email = get_option( $this->option_name . '_email' );

		?>

        <fieldset>
            <label>
                <input type="text" name="<?php echo $this->option_name . '_email' ?>" id="<?php echo $this->option_name . '_email' ?>" value="<?php echo $email; ?>">
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

        ?>

        <fieldset>
            <label>
                <input type="text" name="<?php echo $this->option_name . '_apikey' ?>" id="<?php echo $this->option_name . '_apikey' ?>" value="<?php echo $apikey; ?>">
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

		?>

        <fieldset>
            <label>
                <input type="text" name="<?php echo $this->option_name . '_apiuid' ?>" id="<?php echo $this->option_name . '_apiuid' ?>" value="<?php echo $apiuid; ?>">
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
	 * @return string
	 */
	public function genKey() {

		$api_key = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

		//error_log( "uniq id site key: " . $api_key);

		return $api_key;

	}

}
