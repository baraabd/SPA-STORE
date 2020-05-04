<?php
if (!class_exists('WT_ProdImpExp_Uninstall_Feedback')) :

    /**
     * Uninstall feedback class
     */
    class WT_ProdImpExp_Uninstall_Feedback {

        public function __construct() {
            add_action('admin_footer', array($this, 'deactivate_scripts'));
            add_action('wp_ajax_pipe_submit_uninstall_reason', array($this, "send_uninstall_reason"));
        }

        public function deactivate_scripts() {

            global $pagenow;
            if ('plugins.php' != $pagenow) {
                return;
            }
            $reasons = $this->get_uninstall_reasons();
            ?>
            <div class="pipe-modal" id="pipe-pipe-modal">
                <div class="pipe-modal-wrap">
                    <div class="pipe-modal-header">
                        <h3><?php _e('If you have a moment, please let us know why you are deactivating:', 'product-import-export-for-woo'); ?></h3>
                    </div>
                    <div class="pipe-modal-body">
                        <ul class="reasons">
                            <?php foreach ($reasons as $reason) { ?>
                                <li data-type="<?php echo esc_attr($reason['type']); ?>" data-placeholder="<?php echo esc_attr($reason['placeholder']); ?>">
                                    <label><input type="radio" name="selected-reason" value="<?php echo $reason['id']; ?>"> <?php echo $reason['text']; ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="pipe-modal-footer">
                        <a href="#" class="dont-bother-me"><?php _e('I rather wouldn\'t say', 'product-import-export-for-woo'); ?></a>
                        <button class="button-primary pipe-model-submit"><?php _e('Submit & Deactivate', 'product-import-export-for-woo'); ?></button>
                        <button class="button-secondary pipe-model-cancel"><?php _e('Cancel', 'product-import-export-for-woo'); ?></button>
                    </div>
                </div>
            </div>

            <style type="text/css">
                .pipe-modal {
                    position: fixed;
                    z-index: 99999;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    background: rgba(0,0,0,0.5);
                    display: none;
                }
                .pipe-modal.modal-active {display: block;}
                .pipe-modal-wrap {
                    width: 50%;
                    position: relative;
                    margin: 10% auto;
                    background: #fff;
                }
                .pipe-modal-header {
                    border-bottom: 1px solid #eee;
                    padding: 8px 20px;
                }
                .pipe-modal-header h3 {
                    line-height: 150%;
                    margin: 0;
                }
                .pipe-modal-body {padding: 5px 20px 20px 20px;}
                .pipe-modal-body .input-text,.pipe-modal-body textarea {width:75%;}
                .pipe-modal-body .reason-input {
                    margin-top: 5px;
                    margin-left: 20px;
                }
                .pipe-modal-footer {
                    border-top: 1px solid #eee;
                    padding: 12px 20px;
                    text-align: right;
                }
                .reviewlink{
                    padding:10px 0px 0px 35px !important;
                    font-size: 15px;
                }
                .review-and-deactivate{
                    padding:5px;
                }
            </style>
            <script type="text/javascript">
                (function ($) {
                    $(function () {
                        var modal = $('#pipe-pipe-modal');
                        var deactivateLink = '';


                        $('#the-list').on('click', 'a.pipe-deactivate-link', function (e) {
                            e.preventDefault();
                            modal.addClass('modal-active');
                            deactivateLink = $(this).attr('href');
                            modal.find('a.dont-bother-me').attr('href', deactivateLink).css('float', 'left');
                        });

                        $('#pipe-pipe-modal').on('click', 'a.review-and-deactivate', function (e) {
                            e.preventDefault();
                            window.open("https://wordpress.org/support/plugin/product-import-export-for-woo/reviews/#new-post");
                            window.location.href = deactivateLink;
                        });
                        modal.on('click', 'button.pipe-model-cancel', function (e) {
                            e.preventDefault();
                            modal.removeClass('modal-active');
                        });
                        modal.on('click', 'input[type="radio"]', function () {
                            var parent = $(this).parents('li:first');
                            modal.find('.reason-input').remove();
                            var inputType = parent.data('type'),
                                    inputPlaceholder = parent.data('placeholder');
                            if ('reviewhtml' === inputType) {
                                var reasonInputHtml = '<div class="reviewlink"><a href="#" target="_blank" class="review-and-deactivate"><?php _e('Deactivate and leave a review', 'product-import-export-for-woo'); ?> <span class="xa-pipe-rating-link"> &#9733;&#9733;&#9733;&#9733;&#9733; </span></a></div>';
                            } else {
                                var reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input type="text" class="input-text" size="40" />' : '<textarea rows="5" cols="45"></textarea>') + '</div>';
                            }
                            if (inputType !== '') {
                                parent.append($(reasonInputHtml));
                                parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
                            }
                        });

                        modal.on('click', 'button.pipe-model-submit', function (e) {
                            e.preventDefault();
                            var button = $(this);
                            if (button.hasClass('disabled')) {
                                return;
                            }
                            var $radio = $('input[type="radio"]:checked', modal);
                            var $selected_reason = $radio.parents('li:first'),
                                    $input = $selected_reason.find('textarea, input[type="text"]');

                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pipe_submit_uninstall_reason',
                                    reason_id: (0 === $radio.length) ? 'none' : $radio.val(),
                                    reason_info: (0 !== $input.length) ? $input.val().trim() : ''
                                },
                                beforeSend: function () {
                                    button.addClass('disabled');
                                    button.text('Processing...');
                                },
                                complete: function () {
                                    window.location.href = deactivateLink;
                                }
                            });
                        });
                    });
                }(jQuery));
            </script>
            <?php
        }

        public function send_uninstall_reason() {

            global $wpdb;

            if (!isset($_POST['reason_id'])) {
                wp_send_json_error();
            }



            $data = array(
                'reason_id' => sanitize_text_field($_POST['reason_id']),
                'plugin' => "wfpipe",
                'auth' => 'wfpipe_uninstall_1234#',
                'date' => gmdate("M d, Y h:i:s A"),
                'url' => '',
                'user_email' => '',
                'reason_info' => isset($_REQUEST['reason_info']) ? trim(stripslashes($_REQUEST['reason_info'])) : '',
                'software' => $_SERVER['SERVER_SOFTWARE'],
                'php_version' => phpversion(),
                'mysql_version' => $wpdb->db_version(),
                'wp_version' => get_bloginfo('version'),
                'wc_version' => (!defined('WC_VERSION')) ? '' : WC_VERSION,
                'locale' => get_locale(),
                'multisite' => is_multisite() ? 'Yes' : 'No',
                'wfpipe_version' => WF_PIPE_CURRENT_VERSION
            );
            // Write an action/hook here in webtoffe to recieve the data
            $resp = wp_remote_post('https://feedback.webtoffee.com/wp-json/wfpipe/v1/uninstall', array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => false,
                'body' => $data,
                'cookies' => array()
                    )
            );
            wp_send_json_success($resp);
        }

        private function get_uninstall_reasons() {

            $reasons = array(
                array(
                    'id' => 'used-it',
                    'text' => __('Used it successfully. Don\'t need anymore.', 'product-import-export-for-woo'),
                    'type' => 'reviewhtml',
                    'placeholder' => __('Have used it successfully and aint in need of it anymore', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'could-not-understand',
                    'text' => __('I couldn\'t understand how to make it work', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('Would you like us to assist you?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'found-better-plugin',
                    'text' => __('I found a better plugin', 'product-import-export-for-woo'),
                    'type' => 'text',
                    'placeholder' => __('Which plugin?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'not-have-that-feature',
                    'text' => __('The plugin is great, but I need specific feature that you don\'t support', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('Could you tell us more about that feature?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'is-not-working',
                    'text' => __('The plugin is not working', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('Could you tell us a bit more whats not working?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'looking-for-other',
                    'text' => __('It\'s not what I was looking for', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('Could you tell us a bit more?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'did-not-work-as-expected',
                    'text' => __('The plugin didn\'t work as expected', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('What did you expect?', 'product-import-export-for-woo')
                ),
                array(
                    'id' => 'other',
                    'text' => __('Other', 'product-import-export-for-woo'),
                    'type' => 'textarea',
                    'placeholder' => __('Could you tell us a bit more?', 'product-import-export-for-woo')
                ),
            );

            return $reasons;
        }

    }

    new WT_ProdImpExp_Uninstall_Feedback();

endif;

