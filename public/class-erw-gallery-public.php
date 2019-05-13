<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tureyweb.com/
 * @since      1.0.0
 *
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Erw_Gallery
 * @subpackage Erw_Gallery/public
 * @author     TRW Team  <info@tureyweb.com>
 */
class Erw_Gallery_Public {

	/**
	 * The ID of this plugin.
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Erw_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Erw_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('jquery');

		wp_enqueue_script('imagesloaded-pkgd-js', EG_PUBLIC_JS . 'imagesloaded.pkgd.js', array('jquery'), '' , true);
		wp_enqueue_script('isotope-js', EG_PUBLIC_JS . 'isotope.pkgd.min.js', array('jquery'), '', false);

		wp_enqueue_style( $this->plugin_name, EG_PUBLIC_CSS . 'erw-gallery-public.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, EG_PUBLIC_JS . 'erw-gallery-public.js', array( 'jquery' ), $this->version, false );


	}

}
