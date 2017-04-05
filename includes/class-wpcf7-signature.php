<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html
 * @since      4.0.0
 *
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      4.0.0
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/includes
 * @author     Breizhtorm <tameroski@gmail.com>
 */
class Wpcf7_Signature {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    4.0.0
	 * @access   protected
	 * @var      Wpcf7_Signature_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    4.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    4.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    4.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wpcf7-signature';
		$this->version = '4.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_mail_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    4.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcf7-signature-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcf7-signature-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcf7-signature-mail.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpcf7-signature-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpcf7-signature-public.php';

		// Dependency management
		require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

		$this->loader = new Wpcf7_Signature_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wpcf7_Signature_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    4.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wpcf7_Signature_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    4.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wpcf7_Signature_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'tgmpa_register', $plugin_admin, 'check_dependencies' );

		// WPCF7
		$this->loader->add_filter( 'wpcf7_contact_form_properties', $plugin_admin, 'contact_form_properties', 10, 2 );
		$this->loader->add_action( 'wpcf7_admin_init', $plugin_admin, 'add_tag_generator', 60 );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_upgrade');
		$this->loader->add_action( 'wpcf7_signature_upgrade', $plugin_admin, 'remove_v3_js_callback');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    4.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wpcf7_Signature_Public( $this->get_plugin_name(), $this->get_version() );

		// Scripts and styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// WPCF7
		$this->loader->add_action( 'wpcf7_init', $plugin_public, 'add_signature_shortcode_handler' );
		$this->loader->add_filter( 'wpcf7_validate_signature', $plugin_public, 'signature_validation', 10, 2 );
		$this->loader->add_filter( 'wpcf7_validate_signature*', $plugin_public, 'signature_validation', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the mails sent by CF7
	 *
	 * @since    4.0.0
	 * @access   private
	 */
	private function define_mail_hooks() {

		$plugin_mail = new Wpcf7_Signature_Mail( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'wpcf7_mail_components', $plugin_mail, 'modify_components' );
		$this->loader->add_filter( 'wpcf7_posted_data', $plugin_mail, 'manage_signature_data' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    4.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     4.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     4.0.0
	 * @return    Wpcf7_Signature_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     4.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
