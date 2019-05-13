<?php 
/*
*
* ***** ERW Gallery *****
*
* custom Post Types
* 
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if

$labels = array(
    'name'                => _x( 'Gallery', 'Post Type General Name', $this->plugin_name ),
    'singular_name'       => _x( 'ERW Gallery', 'Post Type Singular Name', $this->plugin_name ),
    'menu_name'           => __( 'Gallery', $this->plugin_name ),
    'name_admin_bar'      => __( 'ERW Gallery', $this->plugin_name ),
    'parent_item_colon'   => __( 'Parent Item:', $this->plugin_name ),
    'all_items'           => __( 'All Gallery', $this->plugin_name ),
    'add_new_item'        => __( 'Add New Gallery', $this->plugin_name ),
    'add_new'             => __( 'Add Gallery', $this->plugin_name ),
    'new_item'            => __( 'New Gallery', $this->plugin_name ),
    'edit_item'           => __( 'Edit Gallery', $this->plugin_name ),
    'update_item'         => __( 'Update Gallery', $this->plugin_name ),
    'search_items'        => __( 'Search Gallery', $this->plugin_name ),
    'not_found'           => __( 'Gallery Not found', $this->plugin_name ),
    'not_found_in_trash'  => __( 'Gallery Not found in Trash', $this->plugin_name ),
);
$args = array(
    'label'               => __( 'ERW Gallery', $this->plugin_name ),
    'description'         => __( 'Custom Post Type For Gallery', $this->plugin_name ),
    'labels'              => $labels,
    'supports'            => array( 'title'),
    'taxonomies'          => array(),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-images-alt2',
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => true,
    'can_export'          => true,
    'has_archive'         => true,      
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
);
register_post_type( 'erw_gallery', $args );
