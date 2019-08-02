<?php
/**
 * Contact Form 7 Signature Addon
 *
 * @link              https://github.com/Breizhtorm/Contact-Form-7-Signature-Addon
 * @since             4.0.0
 * @package           Wpcf7_Signature
 *
 * @wordpress-plugin
 * Plugin Name: Contact Form 7 Signature Addon
 * Plugin URI: https://github.com/Breizhtorm/Contact-Form-7-Signature-Addon
 * Description: Add a signature field type to the popular Contact Form 7 plugin.
 * Author: Breizhtorm
 * Author URI: http://www.breizhtorm.fr
 * Version: 4.2.2
 * Text Domain: contact-form-7-signature-addon
 * Domain Path: /languages
*/

define( 'WPCF7_SIGNATURE_VERSION', '4.2.2' );
define( 'WPCF7_SIGNATURE_PLUGIN_NAME', 'contact-form-7-signature-addon' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcf7-signature.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    4.0.0
 */
function run_wpcf7_signature() {

	$plugin = new Wpcf7_Signature();
	$plugin->run();

}
run_wpcf7_signature();
