<?php
/**
 * Plugin Name: CITIES API
 * Description: Get content from CITIES API. / Holen Sie sich Inhalte von der CITIES API.
 * Version: 1.0.0
 * Author: Citiesapps
 * Author URI: https://about.citiesapps.com/
 * License: GPLv2 or later
 * Text Domain: citiesapps
 */
?>

<?php
// FILE PATHS
// plugin_basename( __FILE__ );            // citiesapps.php
// plugin_dir_path( __FILE__ );            // citiesapps (i.e. plugin folder)
// plugins_url();                          // .../wp-content/plugins
// plugins_url( 'inc', __FILE__ );         // inc folder in citiesapps (i.e. plugin folder)
// plugins_dir_url( __FILE__ );
?>


<?php

// Safety measures
defined( 'ABSPATH' ) or die( 'Unauthorized way of accessing.' );


// Plugin paths & URLs
define( 'CAPLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'CAPLUGIN_DIR',  plugin_dir_path( __FILE__ ) );


// Plugin CSS & JS
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-styles.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-scripts.php' );


// Plugin menus
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-menus.php' );


// Plugin settings/options
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-settings.php' );


// API call
include( plugin_dir_path( __FILE__ ) . 'includes/api.php' );
// include( plugin_dir_path( __FILE__ ) . 'includes/api-frontend.php' );


// Adding plugin settings link
function ca_add_settings_link( $links ) {
    
    $settings_link = '<a href="admin.php?page=cities">' . __( 'Settings', 'citiesapps' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;

}
$plugin_links = "plugin_action_links_" . plugin_basename( __FILE__ );
add_filter( $plugin_links, 'ca_add_settings_link' );


// Creating & registering custom post type 'cities'
function ca_create_posttype() {

    $labels = array(
        'name' => __('CITIES Posts', 'citiesapps'),
        'singular_name' => __('Post', 'citiesapps'),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New Post'),
        'edit_item' => __('Edit Post'),
        'new_item' => __('New Post'),
        'view_item' => __('View Post'),
        'search_items' => __('Search Posts'),
        'not_found' =>  __('Nothing found', 'citiesapps'),
        'not_found_in_trash' => __('Nothing found in Trash', 'citiesapps'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => null,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail','excerpt','comments'),

        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        // 'menu_position'       => 5,
        'can_export'          => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest'        => true,
    );

    register_post_type( 'cities' , $args );
}
add_action( 'init', 'ca_create_posttype' );


// Adding custom post type to query
function ca_add_my_post_types_to_query( $query ) {

    if ( is_home() && $query->is_main_query() )
        $query->set( 'post_type', array( 'post', 'cities' ) );
        return $query;

}
add_action( 'pre_get_posts', 'ca_add_my_post_types_to_query' );




/*

// API (frontend)
function ca_api_func(  $atts ) {

    getApiContentFrontend();

    // die();

}
add_shortcode( 'citiesapps', 'ca_api_func' );


// METABOX
function custom_meta_box() {

    add_meta_box(
        'custom-box',
        __( 'Test Box', 'citiesapps' ),
        'custom_meta_box_field',
        'cities',
        'side', // 'normal'
        // 'low'
    );

}
add_action( 'add_meta_boxes', 'custom_meta_box' );


function custom_meta_box_field() {

    global $post;

    $data = get_post_custom($post->ID);
    $val = isset($data['custom_meta_input']) ? esc_attr($data['custom_meta_input'][0]) : 'no value';

    echo 'Type info here: <input type="text" name="custom_meta_input" id="custom_input" value="'.$val.'" />';

}
// to show it in frontend (e.g. in single.php): $custom_post_type = get_post_meta($post->ID, 'custom_meta_input', true); echo $custom_post_type;


function save_custom_info() {
    
    global $post;
    
    if (define('DOING_AUTOSAVE') && 'DOING_AUTOSAVE') {
        return $post->ID;
    }

    update_post_meta($post->ID, 'custom_meta_input', $_POST['custom_meta_input']);

}
add_action('save_post', 'save_custom_info');
*/

?>