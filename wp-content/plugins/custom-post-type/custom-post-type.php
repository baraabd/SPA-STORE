<?php
/**
* Plugin Name: Custom Post Type
* Description: Custom post type Store.
* Version: 1.0
* Author: Mohsin Rahman
**/
defined( 'ABSPATH' ) or die( 'No script please!' );

class SPAStoreCPT{

function __construct(){
    add_action( 'init',array($this, 'create_SPAStoreCPT') ,0 );
}

function create_SPAStoreCPT(){

$labels = array(
    'name'                => _x( 'Stores', 'Post Type General Name' ),
    'singular_name'       => _x( 'Store', 'Post Type Singular Name' ),
    'menu_name'           => __( 'Stores' ),
    'parent_item_colon'   => __( 'Parent Store' ),
    'all_items'           => __( 'All Stores' ),
    'view_item'           => __( 'View Store' ),
    'add_new_item'        => __( 'Add New Store' ),
    'add_new'             => __( 'Add New' ),
    'edit_item'           => __( 'Edit Store' ),
    'update_item'         => __( 'Update Store' ),
    'search_items'        => __( 'Search Store' ),
    'not_found'           => __( 'Not Found' ),
    'not_found_in_trash'  => __( 'Not found in Trash' ),
);
 
// Set other options for Custom Post Type
 
$args = array(
    'label'               => __( 'Stores' ),
    'description'         => __( 'Store news and reviews' ),
    'labels'              => $labels,
    // Features this CPT supports in Post Editor
    'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
    // You can associate this CPT with a taxonomy or custom taxonomy. 
    'taxonomies'          => array( 'genres' ),
    /* A hierarchical CPT is like Pages and can have
    * Parent and child items. A non-hierarchical CPT
    * is like Posts.
    */ 
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 8,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'post',
    'show_in_rest' => true,

);
 
// Registering your Custom Post Type
register_post_type( 'Stores', $args );
}

}

$spaStoreCPT = new SPAStoreCPT();

