<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kontxt
 * @subpackage Kontxt/admin
 * @author     Michael Bordash <michael@internetdj.com>
 */
class Kontxt_Admin {

    private $option_name    = 'KONTXT';
    private $api_host     = 'http://localhost/wp-json/kontxt/v1/analyze';
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
	public function __construct( $plugin_name, $version )
	{

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;

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

        wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kontxt-functions.js', array( 'jquery', 'wp-rich-text', 'wp-element', 'wp-rich-text' ), $this->version, true );

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

	}

	/**
	 * Handle retrival of analytics results
     *
	 */
	public function kontxt_analyze_results()
    {

        error_log( 'here' );

	    if (!current_user_can('manage_options')) {
		    wp_die('You are not allowed to be on this page.');
	    }

	    check_ajax_referer( 'kontxt-ajax-string', 'security', false );

	    if ( isset( $_POST['dimension'] ) && $_POST['dimension'] !== '' ) {

		    //header('Content-type: application/json');
		    echo $this->kontxt_get_results( $_POST['dimension'] );

	    }

	    exit;
    }


	public function kontxt_get_results( $dimension ) {

		//get and check API key exists, pass key along server side request
		$apiKey = get_option( $this->option_name . '_apikey' );
		$apiUid = get_option( $this->option_name . '_apiuid' );

		if ( !isset($apiKey) || $apiKey === '' ) {

			$response_array['status'] = "error";
			$response_array['message'] = "Your API Key for KONTXT is not set. Please go to Settings > KONTXT to make sure you have a key first.";

			return json_encode($response_array);

		}

		if ( isset( $dimension ) && $dimension !== '' ) {

		    $dimension = sanitize_text_field( $dimension );

			$requestBody = array (
                'api_uid'       => $apiUid,
                'api_key'       => $apiKey,
                'service'       => 'events',
                'event_type'    => $dimension
            );

			$opts = array(
				'body'      => $requestBody,
				'headers'   => 'Content-type: application/x-www-form-urlencoded'
			);

			$response = wp_remote_get($this->api_host, $opts);

			if( $response['response']['code'] === 200 ) {

				return $response['body'];

			} else {

				$response_array['status'] = "error";
				$response_array['message'] = "Plugin Error. Something went wrong with this request. Code received: " . $response['response']['code'];

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

    public function kontxt_cognitive( $textToAnalyze, $service, $requestId )
    {

	    error_log( "request id: " . $requestId );
        //get and check API key exists, pass key along server side request
	    $apiKey = get_option( $this->option_name . '_apikey' );
	    $apiUid = get_option( $this->option_name . '_apiuid' );

        if ( !isset($apiKey) || $apiKey === '' ) {

            $response_array['status'] = "error";
            $response_array['message'] = "Your License Key for Kontxt is not set. Please go to Settings > KONTXT to make sure you have a key first.";

            return json_encode($response_array);

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
                    'request_id'                => $requestId
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

		$this->plugin_screen_hook_suffix = add_management_page(
			__( 'KONTXT Analyze', 'kontxt' ),
			__( 'KONTXT Analyze', 'kontxt' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_analyze_page' )
		);

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
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_options_page()
    {

        $this->plugin_screen_hook_suffix = add_options_page(
            __( 'KONTXT Settings', 'kontxt' ),
            __( 'KONTXT', 'kontxt' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_options_page' )
        );

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
            $this->option_name . '_datasharing',
            __( 'Opt-in to deep analytics and share usage data with KONTXT?', 'kontxt' ),
            array( $this, $this->option_name . '_datasharing_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_datasharing' )
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


        register_setting( $this->plugin_name, $this->option_name . '_datasharing', array( $this, $this->option_name . '_sanitize_option' ) );
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
     * Render the radio input field for datasharing option
     *
     * @since  1.0.0
     */
    public function kontxt_datasharing_cb()
    {

        $datasharing = get_option( $this->option_name . '_datasharing' );

        ?>

        <fieldset>
            <label>
                <input type="radio" name="<?php echo $this->option_name . '_datasharing' ?>" id="<?php echo $this->option_name . '_datasharing' ?>" value="yes" <?php checked( $datasharing, 'yes' ); ?>>
                <?php _e( 'Yes', 'kontxt' ); ?>
            </label>
            <br>
            <label>
                <input type="radio" name="<?php echo $this->option_name . '_datasharing' ?>" value="no" <?php checked( $datasharing, 'no' ); ?>>
                <?php _e( 'No', 'kontxt' ); ?>
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


}
