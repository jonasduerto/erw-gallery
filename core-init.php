<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tureyweb.com/
 * @since      1.0.0
 *
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/includes
 */


// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if

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
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/includes
 * @author     TRW Team  <info@tureyweb.com>
 */
class ERW_GALLERY_CORE_INIT {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Erw_Gallery_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct() {
		global $post;
		$this->constants();
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function constants() {

		if ( defined( 'ERW_GALLERY_VERSION' ) ) {
			$this->version = ERW_GALLERY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'erw-gallery';

		// Define Our Constants
		define( 'EG_PLUGIN_DIR' , plugin_dir_path( __FILE__ ) );
		define( 'EG_PLUGIN_URL' , plugin_dir_url( __FILE__ ) );
		
		define( 'EG_INCLUDES'   , dirname( __FILE__ ) . '/includes/' );
		define( 'EG_ADMIN_DIR'  , dirname( __FILE__ ) . '/admin/' );
		define( 'EG_PUBLIC_DIR' , dirname( __FILE__ ) . '/public/' );
		
		// Admin
		define( 'EG_ADMIN_IMG'  , plugins_url( 'admin/img/', __FILE__ ) );
		define( 'EG_ADMIN_CSS'  , plugins_url( 'admin/css/', __FILE__ ) );
		define( 'EG_ADMIN_JS'   , plugins_url( 'admin/js/', __FILE__ ) );
		
		// Public
		define( 'EG_PUBLIC_IMG' , plugins_url( 'public/img/', __FILE__ ) );
		define( 'EG_PUBLIC_CSS' , plugins_url( 'public/css/', __FILE__ ) );
		define( 'EG_PUBLIC_JS'  , plugins_url( 'public/js/', __FILE__ ) );

		/**
		 * Create a key for the .htaccess secure download link.
		 * @uses    NONCE_KEY     Defined in the WP root config.php
		 */
		define( 'ERW_SECURE_KEY', md5( NONCE_KEY ) );
		
	} // end of constructor function



	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once EG_INCLUDES . 'class-erw-gallery-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once EG_ADMIN_DIR . 'class-erw-gallery-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once EG_PUBLIC_DIR . 'class-erw-gallery-public.php';

		$this->loader = new Erw_Gallery_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Erw_Gallery_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'erw_register_post_gallery' );
		
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, '_admin_add_meta_box' );
		 
		$this->loader->add_action( 'admin_init', $plugin_admin, '_admin_add_meta_box' );
		
		$this->loader->add_action( 'wp_ajax_erw_gallery_js', $plugin_admin, '_ajax_erw_gallery');
	
		$this->loader->add_action( 'save_post', $plugin_admin, '_erw_save_settings');

		//Shortcode
		$this->loader->add_action( 'init', $plugin_admin, 'register_shortcodes');

		//Shortcode Compatibility in Text Widgets
		// $this->loader->add_filter( 'widget_text', $plugin_admin, 'do_shortcode');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Erw_Gallery_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	 * @return    Erw_Gallery_Loader    Orchestrates the hooks of the plugin.
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
