<!-- Template for Settings plugin page -->

<h1>CITIES API</h1> <!-- <h1><?php //esc_html_e( get_admin_page_title() ); ?></h1> -->

<div class="wrap">

    <form method="post" action="options.php">

        <!-- Display necessary hidden fields for settings -->
        <?php settings_fields( 'ca_plugin_settings' ); ?>

        <!-- Display the settings sections for the page -->
        <?php do_settings_sections( 'cities' ); ?>
        
        <!-- Default Submit Button -->
        <?php submit_button(); ?>

    </form>

    <?php
    // Calling function for getting API data
    getApiData();
    ?>
    
</div>

