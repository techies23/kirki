<?php
/**
 * Plugin Name:   Kirki Toolkit
 * Plugin URI:    http://kirki.org
 * Description:   The ultimate WordPress Customizer Toolkit
 * Author:        Aristeides Stathopoulos
 * Author URI:    http://aristeides.com
 * Version:       2.0.1
 * Text Domain:   kirki
 *
 * GitHub Plugin URI: aristath/kirki
 * GitHub Plugin URI: https://github.com/aristath/kirki
 *
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2015, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the autoloader
include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'autoloader.php' );
// Manually include all files to resolve an issue with upgrades for versions < 2.0
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );
}
include_once( dirname( __FILE__ ) . '/includes/class-kirki-active-callback.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-config.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-control.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customize-control.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-branding.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-default-scripts.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-enqueue.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-icons.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-postmessage.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts-tooltips.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer-scripts.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-customizer.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-explode-background-field.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-field-sanitize.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-field.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-google-fonts-registry.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-google-fonts-scripts.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-helper.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-init.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-panel.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-sanitize-values.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-sanitize.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-scripts-registry.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-section.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-settings.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-styles-customizer.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-styles-frontend.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-styles-output-css.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-toolkit.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki-values.php' );
include_once( dirname( __FILE__ ) . '/includes/class-kirki.php' );
include_once( dirname( __FILE__ ) . '/includes/deprecated.php' );
include_once( dirname( __FILE__ ) . '/includes/lib/class-kirki-color.php' );
include_once( dirname( __FILE__ ) . '/includes/lib/class-kirki-colourlovers.php' );

if ( ! function_exists( 'Kirki' ) ) {
	/**
	 * Returns the Kirki object
	 */
	function Kirki() {
		// Make sure the class is instanciated
		$kirki = Kirki_Toolkit::get_instance();

		$kirki->font_registry = new Kirki_Google_Fonts_Registry();
		$kirki->api           = new Kirki();
		$kirki->scripts       = new Kirki_Scripts_Registry();
		$kirki->styles        = array(
			'back'  => new Kirki_Styles_Customizer(),
			'front' => new Kirki_Styles_Frontend(),
		);

		/**
		 * The path of the current Kirki instance
		 */
		Kirki::$path = dirname( __FILE__ );

		return $kirki;

	}

	global $kirki;
	$kirki = Kirki();
}

/**
 * Apply the filters to the Kirki::$url
 */
if ( ! function_exists( 'kirki_filtered_url' ) ) {
	function kirki_filtered_url() {
		$config = apply_filters( 'kirki/config', array() );
		if ( isset( $config['url_path'] ) ) {
			Kirki::$url = esc_url_raw( $config['url_path'] );
		}
	}
	add_action( 'after_setup_theme', 'kirki_filtered_url' );
}

include_once( Kirki::$path . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'deprecated.php' );
// Include the API class
include_once( Kirki::$path . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-kirki.php' );

if ( ! function_exists( 'kirki_load_textdomain' ) ) {
	/**
	 * Load plugin textdomain.
	 *
	 * @since 0.8.0
	 */
	function kirki_load_textdomain() {
		$textdomain = 'kirki';

		// Look for WP_LANG_DIR/{$domain}-{$locale}.mo
		if ( file_exists( WP_LANG_DIR . '/' . $textdomain . '-' . get_locale() . '.mo' ) ) {
			$file = WP_LANG_DIR . '/' . $textdomain . '-' . get_locale() . '.mo';
		}
		// Look for Kirki::$path/languages/{$domain}-{$locale}.mo
		if ( ! isset( $file ) && file_exists( Kirki::$path . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $textdomain . '-' . get_locale() . '.mo' ) ) {
			$file = Kirki::$path . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $textdomain . '-' . get_locale() . '.mo';
		}

		if ( isset( $file ) ) {
			load_textdomain( $textdomain, $file );
		}

		load_plugin_textdomain( $textdomain, false, Kirki::$path . DIRECTORY_SEPARATOR . 'languages' );
	}
	add_action( 'plugins_loaded', 'kirki_load_textdomain' );
}

// Add an empty config for global fields
Kirki::add_config( '' );

/**
 * To enable the demo theme, just add this line to your wp-config.php file:
 * define( 'KIRKI_CONFIG', true );
 * Once you add that line, you'll see a new theme in your dashboard called "Kirki Demo".
 * Activate that theme to test all controls.
 */
if ( defined( 'KIRKI_DEMO' ) && KIRKI_DEMO && file_exists( dirname( __FILE__ ) . '/demo-theme/style.css' ) ) {
	register_theme_directory( dirname( __FILE__ ) );
}
