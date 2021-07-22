<?php

// Load JS on all admin pages
function ca_admin_scripts() {

    wp_enqueue_script( 'ca-admin', CAPLUGIN_URL . 'admin/js/ca-admin.js', ['jquery'], '1.0.0' );
    // wp_enqueue_script( 'ca-admin', CAPLUGIN_URL . 'admin/js/ca-admin.js', ['jquery'] );

}
add_action( 'admin_enqueue_scripts', 'ca_admin_scripts', 100 );


// Load JS on frontend
function ca_frontend_scripts() {

    wp_enqueue_script( 'ca-frontend', CAPLUGIN_URL . 'frontend/js/ca-frontend.js', ['jquery'], '1.0.0' );
    // wp_enqueue_script( 'ca-frontend', CAPLUGIN_URL . 'frontend/js/ca-frontend.js', ['jquery'] );

}
add_action( 'wp_enqueue_scripts', 'ca_frontend_scripts', 100 );


?>