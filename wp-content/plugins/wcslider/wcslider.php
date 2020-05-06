<?php 


/*
Plugin Name: Bxslider for woocommerce
Plugin URI: https://www.test.com/
Description: Adds bxslider into woocommerce 
Version: 1.0.0
Author: Bara Ahmad Mohsin
Author URI: http://yourwebsiteurl.com/
*/



// Error if somone opensthis directly 
defined('ABSPATH') or die ("Go away!!");

//Define a path to the plugin
define('WCSLIDER_PATH',plugin_dir_url(__FILE__));

//Load scripts and styles

function wcslider_scripts()
{
    wp_enqueue_style('bxslider',WCSLIDER_PATH.'/css/jquery.bxslider.min.css');

if( wp_script_is('jquery','enqueued') ){
return;

} else{
    wp_enqueue_script ('jquery');
}
    wp_enqueue_script('bxslider',WCSLIDER_PATH.'/js/jquery.bxslider.min.js',array(), 1, false);
   }

add_action( 'wp_enqueue_scripts','wcslider_scripts' );


//Create a shortcode to display slider
// use :[wcslider]

function wcslider_shortcode() {

$args = array(
    'posts_per_page' => 10,
    'post_type' => 'product',
    'meta_key' => '_thumbnail_id',
);
$slider_products = new WP_Query( $args );
echo "<ul class='slider-products'>";
while ($slider_products->have_posts()): $slider_products->the_post(); ?>
<li>
<a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail('shop_catalog'); ?>
<?php the_title('<h3>','</h3>'); ?>

</a>

</li>


<?php endwhile; wp_reset_postdata();

echo "</ul>";
}

add_shortcode( 'wcslider', 'wcslider_shortcode' );


//Execute bxslider

function wcslider_execute(){?>
<script>
    $ = jQuery.noConflict();
$(document).ready(function(){
  $('.slider-products').bxSlider({
      auto: true,
      minSlides:4,
      maxSlides:4,
      slideWidth:250,
      slideMargin:10,
      moveSlides:1,
  });
});
</script>
<?php

}

add_action( 'wp_footer', 'wcslider_execute' );












?>