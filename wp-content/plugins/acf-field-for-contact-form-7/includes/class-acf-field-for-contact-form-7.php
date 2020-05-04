<?php
/**
 * Class for ACF Field support.
 *
 * @package WordPress
 */

// If check class exists.
if ( ! class_exists( 'ACF_Field_For_Contact_form_7' ) ) {

	/**
	 * Declare class.
	 */
	class ACF_Field_For_Contact_form_7 {

		/**
		 * ACF Settings.
		 *
		 * @var settings
		 */
		var $settings;

		/**
		 * Admin notice message.
		 *
		 * @var message
		 */
		var $message;

		/**
		 * Calling construct.
		 */
		public function __construct() {
			// Setting.
			$this->settings = array(
				'version' => '1.0',
				'url' => plugin_dir_url( __FILE__ ),
				'path' => plugin_dir_path( __FILE__ ),
			);
			// ACF plugin error message.
			$this->message = __( 'This website needs "%s" to run. Please download and activate it', 'acf-field-for-contact-form-7' );
			// Admin notice.
			add_action( 'admin_notices', array( $this, 'acf_cf7_check_acf_is_activate' ) );
			// If check required plugin working OR not.
			if ( ! class_exists( 'acf' ) || ! defined( 'WPCF7_VERSION' ) ) {
				return;
			}
			// version 3.
			add_action( 'init', array( $this, 'acf_cf7_init' ) );
			// version 4+.
			add_action( 'acf/register_fields', array( $this, 'acf_cf7_register_fields' ) );
			// include field ( version 5 ).
			add_action( 'acf/include_field_types', array( $this, 'acf_cf7_include_fields' ) );
		}

		/**
		 * ACF Field Init.
		 */
		public function acf_cf7_init() {
			// If function exists or not.
			if ( function_exists( 'register_field' ) ) {
				register_field( 'acf_field_cf7', plugin_dir_path( __FILE__ ) . 'acf-fields/acf-contact-form-7-v3.php' );
			}
		}

		/**
		 * ACF register fields.
		 */
		public function acf_cf7_register_fields() {
			require_once plugin_dir_path( __FILE__ ) . 'acf-fields/acf-contact-form-7-v4.php';
		}

		/**
		 * ACF5 include field.
		 *
		 * @param int $version Plugin version.
		 */
		public function acf_cf7_include_fields( $version = 5 ) {
			require_once plugin_dir_path( __FILE__ ) . 'acf-fields/acf-contact-form-7-v' . $version . '.php';
		}

		/**
		 * If check ACF plugin activate or not.
		 */
		public function acf_cf7_check_acf_is_activate() {
			if ( ! class_exists( 'acf' ) ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . wp_sprintf( $this->message, 'Advanced Custom Fields' ) . '</p></div>';
			} else if ( ! defined( 'WPCF7_VERSION' ) ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . wp_sprintf( $this->message, 'Contact Form 7' ) . '</p></div>';
			}
		}
	}
}
