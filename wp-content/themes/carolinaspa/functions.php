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

/** Banner with message**/
function carolinaspa_spoil_banner(){?>
    
    <div class="banner-spoil">
        <div class="columns-4">
            <h3><?php the_field('banner_text'); ?></h3>
        </div>
        <div class="columns-8">
            <img src="<?php the_field('banner_image'); ?>">
        </div>
    </div>

<?php



}
add_action('homepage', 'carolinaspa_spoil_banner', 75);

/** Print Features with icons**/
function carolinaspa_display_features() { ?>
    </main>
</div><!--#primary-->
</div><!--.col-full-->
<div class="home-features">
<div class="col-full">
    <div class="columns-4">
        <?php the_field('feature_icon_1'); ?>
        <p><?php the_field('feature_content_1'); ?></p>
    </div>
    <div class="columns-4">
        <?php the_field('feature_icon_2'); ?>
        <p><?php the_field('feature_content_2'); ?></p>
    </div>
    <div class="columns-4">
        <?php the_field('feature_icon_3'); ?>
        <p><?php the_field('feature_content_3'); ?></p>
    </div>
</div>
</div>
<div class="col-full">
<div class="content-area">
    <div class="site-main">
<?php
}
add_action('homepage', 'carolinaspa_display_features', 15);


// Display 3 posts in the homepage
function carolinaspa_homepage_blog_entries(){
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'orderby' => 'date', 
        'order'   => 'DESC'
    );
    $entries = new WP_Query($args);
    ?>

<div class="homepage-blog-entries">
<h2 class="section-title">Latest Blog Entries</h2>
<ul>
            <?php while($entries->have_posts()): $entries->the_post(); ?>
            <li>
                    <?php the_post_thumbnail('blog_entry'); ?>
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                    <div class="entry-content">
                        <header class="entry-header">
                            <p>By: <?php the_author(); ?> | <?php the_time(get_option('date_format')); ?>
                        </header>
                        <?php 
                            $content = wp_trim_words(get_the_content(), 20, '.');
                            echo "<p>" . $content . "</p>";
                        ?>
                        <a href="<?php the_permalink(); ?>" class="entry-link">Read more &raquo;</a>
                    </div>
                </li>
                
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>




</div>

<?php
}
add_action('homepage', 'carolinaspa_homepage_blog_entries', 90);

// Remove the Default WooCommerce Footer and create a new one!
function carolinaspa_footer() {
    remove_action('storefront_footer', 'storefront_credit', 20);
    add_action('storefront_after_footer', 'carolinaspa_new_footer_text', 20);
}
add_action('init', 'carolinaspa_footer');
function carolinaspa_new_footer_text() {
    echo "<div class='reserved'>";
    echo "<p>All Rights Reserved &copy; " . get_bloginfo('name') . " " . get_the_date('Y') . "</p>";
    echo "</div>";
}

// // Display Currency in 3 code digits.
// function carolinaspa_display_sek($symbol, $currency) {
//     $symbol = $currency . " ";
//     return $symbol;
// }
// add_filter('woocommerce_currency_symbol', 'carolinaspa_display_sek', 10, 2);


// Change the number of columns in Shop.
function carolinaspa_shop_columns($columns) {
    return 4;
}
add_filter('loop_shop_columns', 'carolinaspa_shop_columns', 20);


// //Change the number of products per page
//  function carolinaspa_products_per_page($products) {
//     $products = 4;
//      return $products;
//  }
//  add_filter('loop_shop_per_page', 'carolinaspa_products_per_page', 20);


 // Change filter name

function carolinaspa_new_products_title_filter($orderby) {
    $orderby['date'] = __('New Products First');
    return $orderby;
}
add_filter('woocommerce_catalog_orderby', 'carolinaspa_new_products_title_filter', 40);

// Display a Placeholder image when no featured image is added
function carolinaspa_no_featured_image($image_url) {
    $image_url = get_stylesheet_directory_uri() . '/img/no-image.jpg';
    return $image_url;
}
add_filter('woocommerce_placeholder_img_src', 'carolinaspa_no_featured_image');

//  //Removes a tab in the single page product
//  function carolinaspa_remove_description($tabs) {
//     unset($tabs['description']);
//     return $tabs;
//  }
// add_filter('woocommerce_product_tabs', 'carolinaspa_remove_description', 20);

// Change the Title for the description tab
function carolinaspa_title_tab_description($tabs) {
    global $post;
    if($tabs['description']):
        $tabs['description']['title'] = $post->post_title;
    endif;  
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'carolinaspa_title_tab_description', 20);