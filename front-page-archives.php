<?php
/**
 * Front Page Archives
 *
 * @package   FrontPageArchives
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:    Front Page Archives
 * Plugin URI:     https://github.com/lukemcdonald/wordpress-front-page-archives
 * Description:    Allow the front page to display a custom post type archive.
 * Version:        1.0.0
 * Author:         Luke McDonald
 * Author URI:     https://www.lukemcdonald.com/
 * License:        GPL-2.0+
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:    front-page-archives
 * Domain Path:    /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader callback.
 *
 * Converts a class name to a file path and requires it if it exists.
 *
 * @since 1.0.0
 *
 * @param string $class Class name.
 */
function front_page_archives_autoloader( $class ) {
	if ( 0 !== strpos( $class, 'FrontPageArchives_' ) ) {
		return;
	}

	$file  = dirname( __FILE__ ) . '/classes/';
	$file .= str_replace( array( 'FrontPageArchives_', '_' ), array( '', '/' ), $class );
	$file .= '.php';

	if ( file_exists( $file ) ) {
		require_once( $file );
	}
}
spl_autoload_register( 'front_page_archives_autoloader' );

/**
 * Retrieve the main plugin instance.
 *
 * @since 1.0.0
 *
 * @return FrontPageArchives_Plugin
 */
function front_page_archives() {
	static $instance;

	if ( null === $instance ) {
		$instance = new FrontPageArchives_Plugin();
	}

	return $instance;
}

front_page_archives()
	->set_basename( plugin_basename( __FILE__ ) )
	->set_directory( plugin_dir_path( __FILE__ ) )
	->set_file( __FILE__ )
	->set_slug( 'front-page-archives' )
	->set_url( plugin_dir_url( __FILE__ ) );


/**
 * Load the plugin.
 *
 * @since 1.0.0
 */
function front_page_archives_load() {
	front_page_archives()->register_hooks( new FrontPageArchives_Provider_Customize() );
	front_page_archives()->load_plugin();
}
add_action( 'plugins_loaded', 'front_page_archives_load' );
