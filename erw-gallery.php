<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.tureyweb.com/
 * @since             1.0.0
 * @package           erw_gallery
 *
 * Plugin Name:       TuReyWeb's Gallery
 * Plugin URI:        https://www.tureyweb.com
 * Description:       TuReyWeb\'s Gallery is a plugin with lightbox preview for Wordpress
 * Version:           1.0.0
 * Author:            TuReyWeb's Team
 * Author URI:        https://www.tureyweb.com/
 * License:           GPL-2.0+
 * Text Domain:       erw-gallery
 */

// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ERW_GALLERY_VERSION', '1.0.0' );


// Let's Initialize Everything
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . 'core-init.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'core-init.php' );
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_erw_gallery() {

	$plugin = new ERW_GALLERY_CORE_INIT();
	$plugin->run();

}
run_erw_gallery();