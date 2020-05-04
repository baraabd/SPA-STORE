<?php
/**
 * Plugin Name:     ACF Field For Contact Form 7
 * Plugin URI:      https://wordpress.org/plugins/acf-field-for-contact-form-7/
 * Description:     Adds a new 'Contact Form 7' field to the popular Advanced Custom Fields plugin.
 * Author:          KrishaWeb
 * Author URI:      https://www.krishaweb.com/
 * Text Domain:     acf-field-for-contact-form-7
 * Domain Path:     /languages
 * Version:         1.4
 *
 * @package         ACF_Field_For_Contact_Form_7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'includes/class-' . basename( __FILE__ );

/**
 * Plugin textdomain.
 */
function acf_cf7_textdomain() {
	load_plugin_textdomain( 'acf-field-for-contact-form-7', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'acf_cf7_textdomain' );

/**
 * Plugin activation.
 */
function acf_cf7_activation() {
	// Activation code here.
}
register_activation_hook( __FILE__, 'acf_cf7_activation' );

/**
 * Plugin deactivation.
 */
function acf_cf7_deactivation() {
	// Deactivation code here.
}
register_deactivation_hook( __FILE__, 'acf_cf7_deactivation' );

/**
 * Initialization class.
 */
function acf_cf7_init() {
	new ACF_Field_For_Contact_form_7;
}
add_action( 'plugins_loaded', 'acf_cf7_init' );
