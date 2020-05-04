<?php
function carolinaspa_new_products($args){
    $args ['limit']   = 6;
    $args ['columns']   = 3;
    $args ['title']   = 'Just Released';

return $args;

}
add_filter('storefront_recent_products_args','carolinaspa_new_products');



function carolinaspa_setup() {
    add_image_size('blog_entry', 400, 257, true);
}
add_action('after_setup_theme', 'carolinaspa_setup');


//Add styleshits and scripts

function spastore_scripts()
{

    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,700,900|Lora:400,700');
}
add_action('wp_enqueue_scripts', 'spastore_scripts');






remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 35);


