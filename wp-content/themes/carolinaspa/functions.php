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



// Remove the homepage content text and display the feature image
function carolinaspa_homepage_content()
{
    remove_action('homepage', 'storefront_homepage_content');
    add_action('homepage', 'carolinaspa_homepage_coupon', 10);
}
add_action('init', 'carolinaspa_homepage_content');


function carolinaspa_homepage_coupon()
{
    echo "<div class='main-content'>";
    the_post_thumbnail();
    echo "</div>";
}





remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 35);


// Display Home Kits in the Homepage

function carolinaspa_homepage_homekits()
{ ?>

    <div class="homepage-home-kit-category">
        <div class="content">
            <div class="columns-3">

                <?php $home_kit = get_term(18, 'product_cat', ARRAY_A); ?>
                <h2 class="section-title"><?php echo $home_kit['name']; ?></h2>
                <p><?php echo $home_kit['description']; ?></p>
                <a href="<?php echo get_category_link($home_kit['term_id']); ?>">
                        All Products &raquo;
                    </a>
            </div>
            <?php echo do_shortcode('[product_category category="home-kits" per_page="3" orderby="price" order="asc" columns="9" ]'); ?>

        </div>
    </div>
<?php

}

add_action('homepage', 'carolinaspa_homepage_homekits', 23);

