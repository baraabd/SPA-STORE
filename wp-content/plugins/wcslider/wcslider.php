<?php
/**
* Plugin Name: SPA-STORE WC Slider
* Description: Front Page Slider to display most liked and favourit items.
* Version: 1.0
* Author: SPA STORE
**/
defined( 'ABSPATH' ) or die( 'No script please!' );

/* Define Path */

define("WCSLIDER_PATH", plugin_dir_url( '__FILE__' ).'wcslider');

/* Enqueue Style and Script */

function wcslider_scripts(){
    wp_enqueue_style('bxslider','https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.css' );

    wp_deregister_script('jquery');
    wp_register_script('jqueryjs','https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js', array(), 1, false);
    wp_enqueue_script('jqueryjs');
    wp_register_script('bxsliderjs','https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.js', array(), 1, false);
    wp_enqueue_script('bxsliderjs');
/*       wp_enqueue_script('bxsliderjs','https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.js' );
  wp_register_script('bxsliderjs',WCSLIDER_PATH . '/js/jquery.bxslider.js', array(), 1, 'all');
     wp_enqueue_script('bxsliderjs'); */

}

add_action( 'wp_enqueue_scripts','wcslider_scripts' );

// Create Short Code
// Use: [wcslider]

function wcslider_shortcode(){
    $args = array(
        'posts_per_page' => 10,
        'post_type' => 'product',
        'meta_key' => '_thumbnail_id',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'featured',
                'operator' => 'IN'
            )
            ),
    );
    $slider_products = new WP_Query($args);

    echo "<ul class='slider-products' style='text-align:center;'>";
     while ($slider_products->have_posts(  )):  $slider_products->the_post(  ); ?>
         <li>
             <a href="<?php the_permalink(); ?>">
             <?php the_post_thumbnail( 'shop_catalog' ); ?>
             <?php the_title( '<h3>', '</h3>' ); ?>
             </a>
         </li>
    <?php endwhile; wp_reset_postdata(  );
          
        echo  "</ul>";
}

add_shortcode('wcslider','wcslider_shortcode');

//Execute bxSlider

function wcslider_execute(){ ?>
<script>

var j = jQuery.noConflict();
j(document).ready(function(){
  j('.slider-products').bxSlider(
      {
       auto : true,
       minSlides: 4,
       maxSlides: 4,
       slideWidth: 260,
       slideMargin: 10,
       moveSlides: 1
      }
  );
});
</script>
<?php
}

add_action('wp_head', 'wcslider_execute');
?>
<style>
    .page-template-template-homepage .entry-content {
    width: 100% !important;
    max-width: initial !important;
}

.entry-title {
    display: none !important;
}

.page-template-template-homepage:not(.has-post-thumbnail) .site-main {
    padding-top: 0 !important;
}

.page-template-template-homepage .type-page {
    padding-bottom: 0 !important;

}
.page-template-template-homepage .hentry {
    margin-bottom: 2em !important;
}

.hentry .wp-post-image{
    margin-bottom: .8em !important;
}

.slider-products h3{
    font-size:1em;
    font-weight:bold;
}

</style>