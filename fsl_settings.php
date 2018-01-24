<?php

add_action('admin_menu', function() {
    add_options_page( 'Find State Legislators Settings', 'FSL Settings', 'manage_options', 'fsl', 'fsl_plugin_page' );
});

add_action( 'admin_init', function() {
    register_setting( 'fsl-plugin-settings', 'google_geo_api' );
    register_setting( 'fsl-plugin-settings', 'openstates_api' );
});

function fsl_plugin_page() {
?>
<div class="wrap">
<h2>FSL Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'fsl-plugin-settings' ); ?>
    <?php do_settings_sections( 'fsl-plugin-settings' ); ?>
    <hr>

    <div class="field">
    <label for="google_geo_api">Google Geo Api Key</label>
    <input id="google_geo_api" type="text" name="google_geo_api" value="<?php echo esc_attr( get_option('google_geo_api') ); ?>" />
    <p><a href="https://developers.google.com/maps/documentation/geocoding/get-api-key">Get Google Geo Api Key</a></p>
    </div>

    <div class="field">
    <label for="openstates_api">Openstates Api Key</label>
    <input id="openstates_api" type="text" name="openstates_api" value="<?php echo esc_attr( get_option('openstates_api') ); ?>" />
    <p><a href="https://openstates.org/api/register/">Get Openstates Api Key</a></p>
    </div> 
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 

    
