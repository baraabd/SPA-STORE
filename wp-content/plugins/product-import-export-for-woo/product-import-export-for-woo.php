<?php
/*
  Plugin Name: Product CSV Import Export (BASIC)
  Plugin URI: https://wordpress.org/plugins/product-import-export-for-woo/
  Description: Import and Export Products From and To your WooCommerce Store.
  Author: WebToffee
  Author URI: https://www.webtoffee.com/product/product-import-export-woocommerce/
  Version: 1.7.8
  WC tested up to: 4.0.1
  License:           GPLv3
  License URI:       https://www.gnu.org/licenses/gpl-3.0.html
  Text Domain: product-import-export-for-woo
 */

if ( !defined( 'ABSPATH' ) || !is_admin() ) {
	return;
}


if ( !defined( 'WF_PIPE_CURRENT_VERSION' ) ) {
	define( "WF_PIPE_CURRENT_VERSION", "1.7.8" );
}
if ( !defined( 'WF_PROD_IMP_EXP_ID' ) ) {
	define( "WF_PROD_IMP_EXP_ID", "wf_prod_imp_exp" );
}
if ( !defined( 'WF_WOOCOMMERCE_CSV_IM_EX' ) ) {
	define( "WF_WOOCOMMERCE_CSV_IM_EX", "wf_woocommerce_csv_im_ex" );
}
/**
 * Check if WooCommerce is active
 */
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !array_key_exists( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins', array() ) ) ) ) { // deactive if woocommerce in not active
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	deactivate_plugins( plugin_basename( __FILE__ ) );
}
register_activation_hook( __FILE__, 'hf_welcome_screen_activate_basic' );

function hf_welcome_screen_activate_basic() {
	if ( !class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( __( "WooCommerce is required for this plugin to work properly. Please activate WooCommerce.", 'product-import-export-for-woo' ), "", array( 'back_link' => 1 ) );
	}
	if ( is_plugin_active( 'product-csv-import-export-for-woocommerce/product-csv-import-export.php' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( __( "Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='https://www.webtoffee.com/support/'>support</a>", 'product-import-export-for-woo' ), "", array( 'back_link' => 1 ) );
	}
	update_option( 'xa_pipe_plugin_installed_date', date( 'Y-m-d H:i:s' ) );
	set_transient( '_welcome_screen_activation_redirect', true, 30 );
}

if ( !class_exists( 'WF_Product_Import_Export_CSV' ) ) :

	/**
	 * Main CSV Import class
	 */
	class WF_Product_Import_Export_CSV {

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( !defined( 'WF_ProdImpExpCsv_FILE' ) ) {
				define( 'WF_ProdImpExpCsv_FILE', __FILE__ );
			}

			if ( !defined( 'WF_ProdImpExpCsv_BASE' ) ) {
				define( 'WF_ProdImpExpCsv_BASE', plugin_dir_path( __FILE__ ) );
			}

			add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wf_plugin_action_links' ) );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'catch_export_request' ), 20 );
			add_action( 'admin_init', array( $this, 'register_importers' ) );

			add_filter( 'admin_footer_text', array( $this, 'WT_admin_footer_text' ), 100 );
			add_action( 'wp_ajax_pipe_wt_review_plugin', array( $this, "review_plugin" ) );


			include_once( 'includes/class-wf-prodimpexpcsv-system-status-tools.php' );
			include_once( 'includes/class-wf-prodimpexpcsv-admin-screen.php' );
			include_once( 'includes/importer/class-wf-prodimpexpcsv-importer.php' );

			if ( defined( 'DOING_AJAX' ) ) {
				include_once( 'includes/class-wf-prodimpexpcsv-ajax-handler.php' );
			}

			// uninstall feedback catch
			include_once 'includes/class-wf-prodimpexp-plugin-uninstall-feedback.php';
			// Welcome screen tutorial video --> Move this function to inside the class
			add_action( 'admin_init', array( $this, 'impexp_welcome' ) );
			add_action( 'in_plugin_update_message-product-import-export-for-woo/product-import-export-for-woo.php', array( $this, 'wt_product_import_export_for_woo_update_message' ), 10, 2 );
		}

		public function wf_plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wf_woocommerce_csv_im_ex&tab=export' ) . '">' . __( 'Import Export', 'product-import-export-for-woo' ) . '</a>',
				'<a target="_blank" href="https://www.webtoffee.com/product/product-import-export-woocommerce/" style="color:#3db634;"> ' . __( 'Premium Upgrade', 'product-import-export-for-woo' ) . '</a>',
				'<a target="_blank" href="https://wordpress.org/support/plugin/product-import-export-for-woo/">' . __( 'Support', 'product-import-export-for-woo' ) . '</a>',
				'<a target="_blank" href="https://wordpress.org/support/plugin/product-import-export-for-woo/reviews/">' . __( 'Review', 'product-import-export-for-woo' ) . '</a>',
			);
			if ( array_key_exists( 'deactivate', $links ) ) {
				$links[ 'deactivate' ] = str_replace( '<a', '<a class="pipe-deactivate-link"', $links[ 'deactivate' ] );
			}
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Add screen ID
		 */
		public function woocommerce_screen_ids( $ids ) {
			$ids[] = 'admin'; // For import screen
			return $ids;
		}

		/**
		 * Handle localization
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'product-import-export-for-woo', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Catches an export request and exports the data. This class is only loaded in admin.
		 */
		public function catch_export_request() {

			if ( !empty( $_GET[ 'action' ] ) && !empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wf_woocommerce_csv_im_ex' ) {
				switch ( $_GET[ 'action' ] ) {
					case "export" :
						$user_ok = self::hf_user_permission();
						if ( $user_ok ) {
							include_once( 'includes/exporter/class-wf-prodimpexpcsv-exporter.php' );
							WF_ProdImpExpCsv_Exporter::do_export( 'product' );
						} else {
							wp_redirect( wp_login_url() );
						}
						break;
				}
			}
		}

		/**
		 * Register importers for use
		 */
		public function register_importers() {
			register_importer( 'xa_woocommerce_csv', 'WebToffee WooCommerce Product Import (CSV)', __( 'Import <strong>products</strong> to your store via a csv file.', 'product-import-export-for-woo' ), 'WF_ProdImpExpCsv_Importer::product_importer' );
		}

		public static function hf_user_permission() {
			// Check if user has rights to export
			$current_user		 = wp_get_current_user();
			$current_user->roles = apply_filters( 'hf_add_user_roles', $current_user->roles );
			$current_user->roles = array_unique( $current_user->roles );
			$user_ok			 = false;
			$wf_roles			 = apply_filters( 'hf_user_permission_roles', array( 'administrator', 'shop_manager' ) );
			if ( $current_user instanceof WP_User ) {
				$can_users = array_intersect( $wf_roles, $current_user->roles );
				if ( !empty( $can_users ) || is_super_admin( $current_user->ID ) ) {
					$user_ok = true;
				}
			}
			return $user_ok;
		}

		function webtoffee_storefrog_admin_notices() {
			if ( apply_filters( 'webtoffee_storefrog_suppress_admin_notices', false ) || !self::hf_user_permission() ) {
				return;
			}
			$screen				 = get_current_screen();
			$allowed_screen_ids	 = array( 'product_page_wf_woocommerce_csv_im_ex' );

			if ( in_array( $screen->id, $allowed_screen_ids ) || (isset( $_GET[ 'import' ] ) && $_GET[ 'import' ] == 'xa_woocommerce_csv') ) {

				$notice	 = __( '<h3>Save Time, Money & Hassle on Your WooCommerce Data Migration?</h3>', 'product-import-export-for-woo' );
				$notice	 .= __( '<h3>Use StoreFrog Migration Services.</h3>', 'product-import-export-for-woo' );

				$content = '<style>.webtoffee-storefrog-nav-tab.updated {display: flex;align-items: center;margin: 18px 20px 10px 0;padding:23px;border-left-color: #2c85d7!important}.webtoffee-storefrog-nav-tab ul {margin: 0;}.webtoffee-storefrog-nav-tab h3 {margin-top: 0;margin-bottom: 9px;font-weight: 500;font-size: 16px;color: #2880d3;}.webtoffee-storefrog-nav-tab h3:last-child {margin-bottom: 0;}.webtoffee-storefrog-banner {flex-basis: 20%;padding: 0 15px;margin-left: auto;} .webtoffee-storefrog-banner a:focus{box-shadow: none;}</style>';
				$content .= '<div class="updated woocommerce-message webtoffee-storefrog-nav-tab notice is-dismissible"><ul>' . $notice . '</ul><div class="webtoffee-storefrog-banner"><a href="http://www.storefrog.com/" target="_blank"> <img src="' . plugins_url( basename( plugin_dir_path( WF_ProdImpExpCsv_FILE ) ) ) . '/images/storefrog.png"/></a></div><div style="position: absolute;top: 0;right: 1px;z-index: 10000;" ><button type="button" id="webtoffee-storefrog-notice-dismiss" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div></div>';
				echo $content;


				wc_enqueue_js( "
					jQuery( '#webtoffee-storefrog-notice-dismiss' ).click( function() {
                                            
						jQuery.post( '" . admin_url( "admin-ajax.php" ) . "', { action: 'webtoffee_storefrog_notice_dismiss' } );
						jQuery('.webtoffee-storefrog-nav-tab').fadeOut();
					});
				" );
			}
		}

		public function webtoffee_storefrog_notice_dismiss() {

			if ( !self::hf_user_permission() ) {
				wp_die( -1 );
			}
			update_option( 'webtoffee_storefrog_admin_notices_dismissed', 1 );
			wp_die();
		}

		public function WT_admin_footer_text( $footer_text ) {
			if ( !self::hf_user_permission() ) {
				return $footer_text;
			}
			$screen				 = get_current_screen();
			$allowed_screen_ids	 = array( 'product_page_wf_woocommerce_csv_im_ex' );
			if ( in_array( $screen->id, $allowed_screen_ids ) || (isset( $_GET[ 'import' ] ) && $_GET[ 'import' ] == 'xa_woocommerce_csv') ) {
				if ( !get_option( 'pipe_wt_plugin_reviewed' ) ) {
					$footer_text = sprintf(
					__( 'If you like the plugin please leave us a %1$s review.', 'product-import-export-for-woo' ), '<a href="https://wordpress.org/support/plugin/product-import-export-for-woo/reviews?rate=5#new-post" target="_blank" class="wt-review-link" data-rated="' . esc_attr__( 'Thanks :)', 'product-import-export-for-woo' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
					);
					wc_enqueue_js(
					"jQuery( 'a.wt-review-link' ).click( function() {
                                                   jQuery.post( '" . WC()->ajax_url() . "', { action: 'pipe_wt_review_plugin' } );
                                                   jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
                                           });"
					);
				} else {
					$footer_text = __( 'Thank you for your review.', 'product-import-export-for-woo' );
				}
			}

			return '<i>' . $footer_text . '</i>';
		}

		public function review_plugin() {
			if ( !self::hf_user_permission() ) {
				wp_die( -1 );
			}
			update_option( 'pipe_wt_plugin_reviewed', 1 );
			wp_die();
		}

		/*
		 *  Redirect to import export page after the plugin activation. 
		 */

		public function impexp_welcome() {
			if ( !get_transient( '_welcome_screen_activation_redirect' ) ) {
				return;
			}
			delete_transient( '_welcome_screen_activation_redirect' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'wf_woocommerce_csv_im_ex' ), admin_url( 'admin.php' ) ) );
		}

		/*
		 *  Displays update information for the plugin. 
		 */

		public function wt_product_import_export_for_woo_update_message( $data, $response ) {
			if ( isset( $data[ 'upgrade_notice' ] ) ) {
				printf(
				'<div class="update-message wt-update-message">%s</div>', $data[ 'upgrade_notice' ]
				);
			}
		}

	}

	endif;

new WF_Product_Import_Export_CSV();
