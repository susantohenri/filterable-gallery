<?php

/**
 *  Plugin Name: Filterable Gallery
 * 	Plugin URI: http://builderla.com/
 * 	Description: Wordpress plugin for filterable gallery
 * 	Version: 1.0
 * 	Author: Javier Basso
 * 	Author URI: http://www.doctormopar.com
 */

define('FILTERABLE_GALLERY_LOAD_IMAGE_INTERVAL_MS', 100);
define('FILTERABLE_GALLERY_LOAD_IMAGE_LIMIT', 6);
add_shortcode('filterable-gallery', 'filterable_gallery');

function filterable_gallery()
{
    wp_register_style('filterable-gallery', filterable_gallery_get_file('css'), [], '1.0.0');
    wp_enqueue_style('filterable-gallery');
    wp_register_script('filterable-gallery', filterable_gallery_get_file('js'), ['jquery'], '1.0.0');
    wp_enqueue_script('filterable-gallery');
    $json = json_decode(file_get_contents(filterable_gallery_get_file('json')));
    wp_localize_script('filterable-gallery', 'filterable_gallery', [
        'json' => $json,
        'interval_ms' => current_user_can('manage_options') ? 10 : FILTERABLE_GALLERY_LOAD_IMAGE_INTERVAL_MS,
        'limit_lazy' => FILTERABLE_GALLERY_LOAD_IMAGE_LIMIT
    ]);

    include plugin_dir_path(__FILE__) . 'filterable-gallery-html.php';
}

function filterable_gallery_get_file($type)
{
    return plugin_dir_url(__FILE__) . 'filterable-gallery-' . $type . '.' . $type;
}
