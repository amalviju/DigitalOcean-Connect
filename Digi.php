/*
Plugin Name: DigitalOcean Connect
Description: Connect WordPress to DigitalOcean with a custom domain
Author: Amal Viju

*/

// Load required WordPress libraries
require_once ABSPATH . '/wp-admin/includes/plugin.php';
require_once ABSPATH . '/wp-includes/class-http.php';

// Set the plugin version
define( 'DIGITALOCEAN_CONNECT_VERSION', '1.0.0' );

// Enqueue the plugin's scripts and styles
function digitalocean_connect_scripts() {
    wp_enqueue_script( 'digitalocean-connect', plugins_url( 'digitalocean-connect.js', __FILE__ ), array( 'jquery' ), DIGITALOCEAN_CONNECT_VERSION, true );
    wp_enqueue_style( 'digitalocean-connect', plugins_url( 'digitalocean-connect.css', __FILE__ ), array(), DIGITALOCEAN_CONNECT_VERSION );
}
add_action( 'admin_enqueue_scripts', 'digitalocean_connect_scripts' );

// Add the DigitalOcean settings page to the WordPress admin menu
function digitalocean_connect_add_menu() {
    add_menu_page( 'DigitalOcean Connect', 'DigitalOcean Connect', 'manage_options', 'digitalocean-connect', 'digitalocean_connect_settings_page' );
}
add_action( 'admin_menu', 'digitalocean_connect_add_menu' );

// Define the DigitalOcean settings page HTML
function digitalocean_connect_settings_page() {
    // Get the DigitalOcean API token from the WordPress database
    $digitalocean_token = get_option( 'digitalocean_connect_token' );
    
    // Check if the API token is valid
    $digitalocean_api = new WP_Http();
    $digitalocean_response = $digitalocean_api->request( 'https://api.digitalocean.com/v2/account', array( 'headers' => array( 'Authorization' => 'Bearer ' . $digitalocean_token ) ) );
    $digitalocean_body = json_decode( $digitalocean_response['body'] );
    if ( isset( $digitalocean_body->id ) ) {
        $digitalocean_valid_token = true;
    } else {
        $digitalocean_valid_token = false;
    }
    
    // Get the DigitalOcean domain name from the WordPress database
    $digitalocean_domain = get_option( 'digitalocean_connect_domain' );
    
    // Display the DigitalOcean settings page HTML
    ?>
    <div class="digitalocean-connect-wrapper">
        <h1>DigitalOcean Connect</h1>
        <?php if ( $digitalocean_valid_token ) : ?>
            <form method="post" action="options.php">
                <?php settings_fields( 'digitalocean-connect-settings' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">DigitalOcean API Token</th>
                        <td><input type="text" name="digitalocean_connect_token" value="<?php echo esc_attr( $digitalocean_token ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Custom Domain Name</th>
                        <td><input type="text" name="digitalocean_connect_domain" value="<?php echo esc_attr( $digitalocean_domain ); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        <?php else : ?>
            <p class="error">Invalid DigitalOcean API Token</p>
        <?php endif; ?>
    </div>
    <?php
}

// Register the DigitalOcean settings with the
