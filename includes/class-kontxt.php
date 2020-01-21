<?php


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Kontxt
 * @subpackage Kontxt/includes
 * @author     Michael Bordash <michael@internetdj.com>
 */
class Kontxt {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kontxt_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;
	protected $plugin_name;
	protected $version;
	protected $option_name;
	protected $api_host;
	protected $kontxt_ini;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @param $kontxt_ini
	 *
	 * @since    1.0.0
	 */
	public function __construct($kontxt_ini) {

		$this->plugin_name      = 'kontxt';
		$this->version          = '1.2.0';
		$this->option_name      = 'KONTXT';
		$this->kontxt_ini       = $kontxt_ini;
		$this->api_host         = $kontxt_ini['api_host'];

		$this->load_dependencies();
		$this->set_locale();

		$this->define_public_hooks();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Kontxt_Loader. Orchestrates the hooks of the plugin.
	 * - Kontxt_i18n. Defines internationalization functionality.
	 * - Kontxt_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kontxt-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kontxt-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kontxt-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-kontxt-public.php';

		$this->loader = new Kontxt_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Kontxt_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Kontxt_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Kontxt_Admin( $this->get_plugin_name(), $this->get_version(), $this->option_name, $this->api_host );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// load an action to handle the incoming ajax request for text analysis
		$this->loader->add_action( 'wp_ajax_kontxt_analyze_results', $plugin_admin, 'kontxt_analyze_results');
		$this->loader->add_action( 'wp_ajax_kontxt_analyze', $plugin_admin, 'kontxt_process_text');

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_management_page' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );

    }

	/**
	 * Register all of the hooks related to the public area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Kontxt_Public( $this->get_plugin_name(), $this->get_version(), $this->option_name, $this->api_host );

		$optin = get_option( $this->option_name . '_optin' );

		if( $optin === 'yes' ) {

			// fix for empty user object using wpcf7 plugin
			if( in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins') ) ) ) {

				add_filter( 'wpcf7_verify_nonce', '__return_true' );

			}

			if ( false === wp_doing_cron() ) {
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			}

			// capture all page state information from site user
			//$this->loader->add_action( 'wp', $plugin_public, 'kontxt_capture_session');

			// capture page/product view events
			$this->loader->add_action( 'user_register', $plugin_public, 'kontxt_user_register', 15 );

			// capture page/product view events
			$this->loader->add_action( 'wp_ajax_kontxt_send_event', $plugin_public, 'kontxt_send_event', 15 );

			// capture page/product view events
			$this->loader->add_action( 'wp_ajax_nopriv_kontxt_send_event', $plugin_public, 'kontxt_send_event', 15 );

			// capture sentiment on comment post
			$this->loader->add_action( 'comment_post', $plugin_public, 'kontxt_comment_post', 15 );

			// capture woo commerce checkout confirmed
			$this->loader->add_action( 'woocommerce_checkout_order_processed', $plugin_public, 'kontxt_order_post', 15 );

			// capture woo commerce add to cart
			$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'kontxt_cart_capture', 15 );

			// capture contact form 7 mail sent
			$this->loader->add_action( 'wpcf7_posted_data', $plugin_public, 'kontxt_contact_form_capture', 15 );

			// capture gravity form submission
			$this->loader->add_action( 'gform_after_submission', $plugin_public, 'kontxt_contact_form_capture', 15 );


		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Kontxt_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}

}