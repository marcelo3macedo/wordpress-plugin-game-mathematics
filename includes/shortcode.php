<?php
if (!defined('ABSPATH')) exit;

add_shortcode('jogo-soma-dos-dedinhos', 'jdsd_render_shortcode');

function jdsd_render_shortcode() {
    $plugin_url = plugin_dir_url(__FILE__) . '../';
    $index_path = plugin_dir_path(__FILE__) . '../dist/index.html';

    if (!file_exists($index_path)) {
        if (current_user_can('manage_options')) {
             return '<p style="color:red;">O jogo ainda não foi buildado. Execute <code>npm run build</code> na pasta do projeto.</p>';
        }
        return '';
    }

    $html = file_get_contents($index_path);

    $html = preg_replace(
        '/(src|href)="\.\/(.*?)"/',
        '$1="' . $plugin_url . 'dist/$2"',
        $html
    );

    $html = preg_replace(
        '/(src|href)="\/(assets\/.*?)"/',
        '$1="' . $plugin_url . 'dist/$2"',
        $html
    );
    
    return $html;
}