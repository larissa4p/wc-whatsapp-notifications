<?php
/**
 * Plugin Name:       Evolution WhatsApp
 * Plugin URI:        https://github.com/larissa4p/wc-whatsapp-notifications
 * Description:       Notificações WooCommerce e widget de chat via Evolution API (WhatsApp).
 * Version:           1.0.0
 * Author:            Larissa Bessa
 * Author URI:        https://portfolio-larissa4p.vercel.app
 * License:           GPL v2 or later
 * Text Domain:       evolution-whatsapp
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * WC requires at least: 7.0
 */

defined( 'ABSPATH' ) || exit;

define( 'EWA_VERSION',  '1.0.0' );
define( 'EWA_FILE',     __FILE__ );
define( 'EWA_DIR',      plugin_dir_path( __FILE__ ) );
define( 'EWA_URL',      plugin_dir_url( __FILE__ ) );
define( 'EWA_SLUG',     'evolution-whatsapp' );

require_once EWA_DIR . 'includes/class-evolution-api.php';
require_once EWA_DIR . 'includes/class-settings.php';
require_once EWA_DIR . 'includes/class-woo-notifications.php';
require_once EWA_DIR . 'includes/class-chat-widget.php';

function ewa_init(): void {
    new EWA_Settings();
    new EWA_Chat_Widget();

    if ( class_exists( 'WooCommerce' ) ) {
        new EWA_Woo_Notifications();
    }
}
add_action( 'plugins_loaded', 'ewa_init' );
