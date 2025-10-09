<?php
/**
 * Plugin Name: Jogo Soma dos Dedinhos
 * Description: Exibe o jogo "Soma dos Dedinhos" (buildado com Vite).
 * Version: 1.1
 * Author: Marcelo Macedo
 */

if (!defined('ABSPATH')) exit;

add_shortcode('jogo-soma-dos-dedinhos', 'render_jogo_soma_dos_dedinhos');

function render_jogo_soma_dos_dedinhos() {
    $plugin_url = plugin_dir_url(__FILE__);
    $index_path = plugin_dir_path(__FILE__) . 'dist/index.html';

    if (!file_exists($index_path)) {
        return '<p>O jogo ainda não foi buildado. Execute <code>npm run build</code>.</p>';
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

