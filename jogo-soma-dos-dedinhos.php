<?php
/**
 * Plugin Name: Jogo Soma dos Dedinhos
 * Description: Exibe o jogo "Soma dos Dedinhos" (buildado com Vite).
 * Version: 1.1
 * Author: Marcelo Macedo
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
define('JOGO_DEDINHOS_TABLE', $wpdb->prefix . 'jogo_dos_dedinhos');


require_once plugin_dir_path( __FILE__ ) . 'includes/database.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';

register_activation_hook( __FILE__, 'jdsd_create_database_table' );

add_action( 'rest_api_init', function () {
    $api = new JDS_API();
    $api->register_routes();
});