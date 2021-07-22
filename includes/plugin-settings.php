<?php

// Main function for plugin settings
function ca_plugin_settings() {

  // If plugin settings don't exist, then create them
  if( false == get_option( 'ca_plugin_settings' ) ) {
      add_option( 'ca_plugin_settings' );
  }

  // Define (at least) one section for our fields
  add_settings_section(
    // Unique identifier for the section
    'ca_plugin_settings_section',
    // Section Title
    __( 'Settings', 'citiesapps' ),
    // Callback for an optional description
    'ca_plugin_settings_section_callback',
    // Admin page to add section to (can be seen in plugin menu -- slug that we specify)
    'cities'
  );

  // Adding 'entity_id' field (Input)
  add_settings_field(
    // Unique identifier for entity id
    'ca_entity_id',
    // 'ca_plugin_settings_entity_id',
    // Field Title
    __( 'Entity ID', 'citiesapps'),
    // Callback for field markup
    'ca_plugin_settings_entity_id_callback',
    // Page to go on
    'cities',
    // Section to go in
    'ca_plugin_settings_section'
  );

  // Adding 'content_type' field (Select)
  add_settings_field(
    'ca_content_type',
    // 'ca_plugin_settings_content_type',
    __( 'Content Type', 'citiesapps'),
    'ca_plugin_settings_content_type_callback',
    'cities',
    'ca_plugin_settings_section',
    [
      'news' => 'News',
      'events' => 'Events',
    ]
  );

  // Registering settings
  register_setting(
      'ca_plugin_settings', // group
      'ca_plugin_settings' // specific setting
  );

}
add_action( 'admin_init', 'ca_plugin_settings' );


// Instructions
function ca_plugin_settings_section_callback() {

  esc_html_e( 'Fill out following fields. / Füllen Sie die folgenden Felder aus.', 'citiesapps' );

}


// Callback function for entity_id option
function ca_plugin_settings_entity_id_callback() {

  $options = get_option( 'ca_plugin_settings' );

    $entity_id = '';
    if( isset( $options[ 'entity_id' ] ) ) {
        $entity_id = esc_html( $options['entity_id'] );
    }

  echo '<input type="text" id="ca_entity_id" name="ca_plugin_settings[entity_id]" value="' . $entity_id . '" />';
  
}


// Callback function for content_type option
function ca_plugin_settings_content_type_callback( $args ) {

  $options = get_option( 'ca_plugin_settings' );

  $content_type = '';
  if( isset( $options[ 'content_type' ] ) ) {
    $content_type = esc_html( $options['content_type'] );
  }

  $html = '<select id="ca_content_type" name="ca_plugin_settings[content_type]">';

  $html .= '<option value="news"' . selected( $content_type, 'news', false) . '>' . $args['news'] . '</option>';
  $html .= '<option value="events"' . selected( $content_type, 'events', false) . '>' . $args['events'] . '</option>';

  $html .= '</select>';

  echo $html;

}

?>