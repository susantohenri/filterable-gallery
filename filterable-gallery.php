<?php

/**
 *  Plugin Name: Filterable Gallery
 * 	Plugin URI: http://builderla.com/
 * 	Description: Wordpress plugin for filterable gallery
 * 	Version: 1.0
 * 	Author: Javier Basso
 * 	Author URI: http://www.doctormopar.com
 */

define('FILTERABLE_GALLERY_LOAD_IMAGE_INTERVAL_MS', 500);
define('FILTERABLE_GALLERY_LOAD_IMAGE_LIMIT', 6);
add_shortcode('filterable-gallery', 'filterable_gallery');
add_action('wp_ajax_filterable_gallery_update_json', 'filterable_gallery_update_json');
add_action('wp_ajax_filterable_gallery_add_item', 'filterable_gallery_add_item');

function filterable_gallery()
{
    $filterable_gallery = [
        'json' => filterable_gallery_get_json(),
        'is_admin' => current_user_can('manage_options')
    ];
    wp_register_style('filterable-gallery', filterable_gallery_get_file('css'), [], '1.0.0');
    wp_enqueue_style('filterable-gallery');
    wp_register_script('filterable-gallery', filterable_gallery_get_file('js'), ['jquery'], '1.0.0');
    wp_enqueue_script('filterable-gallery');

    wp_localize_script('filterable-gallery', 'filterable_gallery', [
        'json' => $filterable_gallery['json'],
        'interval_ms' => $filterable_gallery['is_admin'] ? 10 : FILTERABLE_GALLERY_LOAD_IMAGE_INTERVAL_MS,
        'limit_lazy' => $filterable_gallery['is_admin'] ? count($filterable_gallery['json']) : FILTERABLE_GALLERY_LOAD_IMAGE_LIMIT,
        'is_admin' => $filterable_gallery['is_admin'],
        'update_json_url' => admin_url('admin-ajax.php')
    ]);

    include plugin_dir_path(__FILE__) . 'filterable-gallery-html.php';
}

function filterable_gallery_get_json()
{
    $json = file_get_contents(filterable_gallery_get_file('json'));
    $json = 'null' == $json ? [] : json_decode($json);
    return $json;
}

function filterable_gallery_set_json($json)
{
    file_put_contents(plugin_dir_path(__FILE__) . 'filterable-gallery-json.json', json_encode($json));
    return json_encode(filterable_gallery_get_json());
}

function filterable_gallery_get_file($type)
{
    return plugin_dir_url(__FILE__) . 'filterable-gallery-' . $type . '.' . $type;
}

function filterable_gallery_update_json()
{
    exit(filterable_gallery_set_json($_POST['json']));
}

function filterable_gallery_add_item()
{
    if (move_uploaded_file($_FILES['img']['tmp_name'], plugin_dir_path(__FILE__) . 'uploads/' . $_FILES['img']['name'])) {
        $json = filterable_gallery_get_json();
        $json[] = [
            'src' => plugin_dir_url(__FILE__) . 'uploads/' . $_FILES['img']['name'],
            'filter' => $_POST['filter']
        ];
        exit(filterable_gallery_set_json($json));
    }
}
