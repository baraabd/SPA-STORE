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




    wp_enqueue_script('bxslider',WCSLIDER_PATH.'/js/jquery.bxslider.min.js');
   
}

add_action( 'wp_enqueue_scripts','wcslider_scripts' );





















?>