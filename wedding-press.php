<?php
/*
Plugin Name: WeddingPress
Plugin URI: https://jacob-t-graham.com/projects
Description: Sets up your site with everything you need to plan for your big day!
Version: 1.0.0
Author: Jacob Graham
Author URI: https://jacob-t-graham.com/
Text Domain: weddingpress
*/

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// define plugin controller
class WeddingPress
{
    // Constructor.
    public function __construct()
    {
        // enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_resources'));

        //add post types
        add_action('init', array($this, 'registry_item'));
        add_action('add_meta_boxes', array($this, 'add_registry_meta_boxes'));
        add_action('save_post', array($this, 'save_registry_item_settings'));


    }

    // enqueue scripts and styles
    public function enqueue_resources()
    {
        
    }

    //define registry item post type
    public function registry_item()
    {
        //set up post type metadata
        $labels = array(
            'name'                  => _x( 'Registry Items', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Registry Item', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Registry Items', 'text_domain' ),
            'name_admin_bar'        => __( 'Registry Items', 'text_domain' ),
            'archives'              => __( 'Registry Item Archives', 'text_domain' ),
            'attributes'            => __( 'Item Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
            'all_items'             => __( 'All Items', 'text_domain' ),
            'add_new_item'          => __( 'Add New Registry Item', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Item', 'text_domain' ),
            'edit_item'             => __( 'Edit Item', 'text_domain' ),
            'update_item'           => __( 'Update Item', 'text_domain' ),
            'view_item'             => __( 'View Item', 'text_domain' ),
            'view_items'            => __( 'View Items', 'text_domain' ),
            'search_items'          => __( 'Search Item', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
            'items_list'            => __( 'Items list', 'text_domain' ),
            'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
        );
        $args = array(
            'label'                 => __( 'Registry Item', 'text_domain' ),
            'description'           => __( 'An item on the wedding registry.', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'            => array( 'registry' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-products',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );

        //register the post type
        register_post_type( 'registry_item', $args );
        register_post_meta( 'registry_item', 'link', array('type' => 'string', 'single'=>true, 'default'=>'', 'description' => 'A link to the registry item, so guests can buy it online.') );

    }

    public function add_registry_meta_boxes($post){
        //meta box for link to registry item
        add_meta_box(
            'registry_item_link',
            'Item Link',
            function($post){
                $current_val = get_post_meta($post->ID, 'registry_item_link', true);
                wp_nonce_field('registry_item_link_nonce', 'registry_item_link_nonce_field');
                ?> 
                <label for="registry_item_link_field">Enter link to the item:</label>
                <input type="text" id="registry_item_link_field" name="registry_item_link_field" value=<?= esc_attr($current_val) ?>  >
                <?php
            },
            'registry_item',
            'normal',
            'default'
        );
    }

    public function save_registry_item_settings($post_id){
        //check if the current user has permission to save the meta box
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        //verify the nonce field
        if (!isset($_POST['registry_item_link_nonce_field']) || !wp_verify_nonce($_POST['registry_item_link_nonce_field'], 'registry_item_link_nonce')) {
            return;
        }
    
        //update the meta box value
        if (isset($_POST['registry_item_link_field'])) {
            update_post_meta($post_id, 'registry_item_link', sanitize_text_field($_POST['registry_item_link_field']));
        }
    }
}

$plugin = new WeddingPress();

?>