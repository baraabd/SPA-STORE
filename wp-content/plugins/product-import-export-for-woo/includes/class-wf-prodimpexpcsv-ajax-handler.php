<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdImpExpCsv_AJAX_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_woocommerce_csv_import_request', array( $this, 'csv_import_request' ) );
		add_action( 'wp_ajax_woocommerce_csv_import_regenerate_thumbnail', array( $this, 'regenerate_thumbnail' ) );
	}
	
	/**
	 * Ajax event for importing a CSV
	 */
	public function csv_import_request() {
		define( 'WP_LOAD_IMPORTERS', true );
                WF_ProdImpExpCsv_Importer::product_importer();
	}

	/**
	 * From regenerate thumbnails plugin
	 */
	public function regenerate_thumbnail() {
                $nonce = (isset($_POST['wt_nonce']) ? sanitize_text_field($_POST['wt_nonce']) : '');
                if (!wp_verify_nonce($nonce,WF_PROD_IMP_EXP_ID) || !WF_Product_Import_Export_CSV::hf_user_permission()) {
                    wp_die(__('Access Denied', 'product-import-export-for-woo'));
                }
		@error_reporting( 0 ); // Don't break the JSON result

		header( 'Content-type: application/json' );

		$id    = absint($_REQUEST['id']);
		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) )
			die( json_encode( array( 'error' => sprintf( __( 'Failed resize: %s is an invalid image ID.', 'product-import-export-for-woo' ), esc_html( $_REQUEST['id'] ) ) ) ) );

		if ( ! current_user_can( 'manage_woocommerce' ) )
			$this->die_json_error_msg( $image->ID, __( "Your user account doesn't have permission to resize images", 'product-import-export-for-woo' ) );

		$fullsizepath = get_attached_file( $image->ID );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) )
			$this->die_json_error_msg( $image->ID, sprintf( __( 'The originally uploaded image file cannot be found at %s', 'product-import-export-for-woo' ), '<code>' . esc_html( $fullsizepath ) . '</code>' ) );

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) )
			$this->die_json_error_msg( $image->ID, $metadata->get_error_message() );
		if ( empty( $metadata ) )
			$this->die_json_error_msg( $image->ID, __( 'Unknown failure reason.', 'product-import-export-for-woo' ) );

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		die( json_encode( array( 'success' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.', 'product-import-export-for-woo' ), esc_html( get_the_title( $image->ID ) ), $image->ID, timer_stop() ) ) ) );
	}	

	/**
	 * Die with a JSON formatted error message
	 */
	public function die_json_error_msg( $id, $message ) {
        die( json_encode( array( 'error' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s', 'regenerate-thumbnails' ), esc_html( get_the_title( $id ) ), $id, $message ) ) ) );
    }	
}

new WF_ProdImpExpCsv_AJAX_Handler();