<?php
if (!defined('ABSPATH')) {
    exit;
}

$ftp_server = '';
$ftp_user = '';
$ftp_password = '';
$use_ftps = '';
$enable_ftp_ie = '';
$ftp_server_path = '';
if (!empty($ftp_settings)) {
    $ftp_server = $ftp_settings['ftp_server'];
    $ftp_user = $ftp_settings['ftp_user'];
    $ftp_password = $ftp_settings['ftp_password'];
    $use_ftps = $ftp_settings['use_ftps'];
    $enable_ftp_ie = $ftp_settings['enable_ftp_ie'];
    $ftp_server_path = $ftp_settings['ftp_server_path'];
}
?>
<div class="woocommerce">

    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex'); ?>" class="nav-tab"><?php _e('Product Export', 'product-import-export-for-woo'); ?></a>
        <a href="<?php echo admin_url('admin.php?import=xa_woocommerce_csv'); ?>" class="nav-tab nav-tab-active"><?php _e('Product Import', 'product-import-export-for-woo'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex&tab=help'); ?>" class="nav-tab"><?php _e('Help', 'product-import-export-for-woo'); ?></a>
        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="nav-tab nav-tab-premium"><?php _e('Upgrade to Premium for More Features', 'product-import-export-for-woo'); ?></a>
    </h2>

    <div class="pipe-main-box">
        <div class="pipe-view bg-white p-20p">
            <h3 class="title"><?php _e('Step 1: Import settings', 'product-import-export-for-woo'); ?></h3>
            <?php if (!empty($upload_dir['error'])) : ?>
                <div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'product-import-export-for-woo'); ?></p>
                    <p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
            <?php else : ?>
                <div class="tool-box">
                    <p><?php _e('You can import products (in CSV format) in to the shop using by uploading a CSV file.', 'product-import-export-for-woo'); ?></p>
                    <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th>
                                        <label for="upload"><?php _e('Select a file from your computer', 'product-import-export-for-woo'); ?></label>
                                    </th>
                                    <td>
                                        <input type="file" id="upload" name="import" size="25" />
                                        <input type="hidden" name="action" value="save" />
                                        <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" /><br/>
                                        <small><?php _e('Please upload UTF-8 encoded CSV', 'product-import-export-for-woo'); ?> &nbsp; -- &nbsp; <?php printf(__('Maximum size: %s', 'product-import-export-for-woo'), $size); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Update products if exists', 'product-import-export-for-woo'); ?></label><br/></th>
                                    <td>
                                        <input type="checkbox" name="merge" id="merge">
                                        <p><small><?php _e('Existing products are identified by their SKUs/IDs. If this option is not selected and if a product with same ID/SKU is found in the CSV, that product will not be imported.', 'product-import-export-for-woo'); ?></small></p>
                                    </td>
                            
                                </tr>                                
                                <tr>
                                    <th><label><?php _e('Delimiter', 'wf_csv_import_export'); ?></label><br/></th>
                                     <td> 
                                        <input type="radio" id="wt_prod_delimiter_comma" name="delimiter" value="," checked >
                                        <label for="wt_prod_delimiter_comma">Comma</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_tab" name="delimiter" value="tab" >
                                        <label for="wt_prod_delimiter_tab">Tab</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_semi" name="delimiter" value=";" >
                                        <label for="wt_prod_delimiter_semi">Semicolon</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_space" name="delimiter" value="space" >
                                        <label for="wt_prod_delimiter_space">Space</label>&nbsp;&nbsp;&nbsp;
                                        <input type="radio" id="wt_prod_delimiter_other" name="delimiter" value="" >
                                        <label for="wt_prod_delimiter_other">Other</label>&nbsp;&nbsp;&nbsp;
                                         <input type="text" id="wt_prod_delimiter_other_txt" placeholder="," name="delimitertxt"  maxlength="1" size="2" />
                                       <span class="description"><?php _e('<br />Select a delimiter to separate the field values with. ‘,’ is the default delimiter', 'wf_csv_import_export'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Merge empty cells', 'product-import-export-for-woo'); ?></label><br/></th>
                                    <td><input type="checkbox" name="merge_empty_cells" placeholder="," size="2" />
                                        <p><small><?php _e('If this option is checked, empty attributes will be added to products with no value. You can leave this unchecked if you are not sure about this option.', 'product-import-export-for-woo'); ?></small></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button button-primary" value="<?php esc_attr_e('Proceed to Import Mapping', 'product-import-export-for-woo'); ?>" />
                            <p><span><i><?php _e('If you want to import from an FTP location or from a URL or to configure a scheduled import you may need to upgrade to premium version.', 'product-import-export-for-woo'); ?></i></span></p>
                        </p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php include(WF_ProdImpExpCsv_BASE . 'includes/views/market.php'); ?>
        <div class="clearfix"></div>
    </div>
</div>
<script>
    jQuery("#wt_prod_delimiter_other").click(function() {
        jQuery("#wt_prod_delimiter_other_txt").focus();
     });

    jQuery("#wt_prod_delimiter_other_txt").focus(function() {
        jQuery("#wt_prod_delimiter_other").prop("checked", true);
        
    });
    
    jQuery("#wt_import_greet").click(function () {
        var delimiter = jQuery("#wt_prod_delimiter_other_txt").val();
        jQuery('#wt_prod_delimiter_other').val(delimiter);
        });
    
</script>