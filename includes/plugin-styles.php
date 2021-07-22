<?php

// Load CSS on all admin pages
function ca_admin_styles() {

    // wp_enqueue_style( 'ca-admin', CAPLUGIN_URL . 'admin/css/ca-admin.css', [], '1.0.0' );
    wp_enqueue_style( 'ca-admin', CAPLUGIN_URL . 'admin/css/ca-admin.css' );

}
add_action( 'admin_enqueue_scripts', 'ca_admin_styles', 100 );


// Load CSS on frontend
function ca_frontend_styles() {

    // wp_enqueue_style( 'ca-frontend', CAPLUGIN_URL . 'frontend/css/ca-frontend.css', [], '1.0.0' );
    wp_enqueue_style( 'ca-frontend', CAPLUGIN_URL . 'frontend/css/ca-frontend.css' );

}
add_action( 'wp_enqueue_scripts', 'ca_frontend_styles', 100 );


?>