<?php

// Markup for Plugin menu
function ca_plugin_settings_page_markup() {

  // Double check user capabilities
  if ( !current_user_can('manage_options') ) {
      return;
  }
  
  include( CAPLUGIN_DIR . 'templates/admin/settings-page.php');
  
}


// Adding Plugin menu
function ca_plugin_menu() {

    add_menu_page(
        'Citiesapps',                       // Plugin mame
        'CITIES',                           // Plugin menu
        'manage_options',                   // Minimum capability (manage_options is an easy way to target Admins)
        'cities',                           // Menu slug
        'ca_plugin_settings_page_markup',   // Callback that prints the markup
        // 'dashicons-shortcode',              // (Dash)icon
        'dashicons-admin-plugins',          // (Dash)icon
        100                                 // Lower the number, lower it shows in menu
    );

}
add_action( 'admin_menu', 'ca_plugin_menu' );


?>