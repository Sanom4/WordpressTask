<?php
// Enqueue styles
function my_theme_styles()
{
    wp_enqueue_style('my-theme-styles', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_theme_styles');

// Enqueue scripts
function my_theme_scripts()
{
    wp_enqueue_script('my-theme-scripts', get_template_directory_uri() . '/js/util.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('my-theme-cart', get_template_directory_uri() . '/js/cart.js', array('jquery'), '1.0.0', true);
    wp_localize_script('my-theme-cart', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('my-theme-css', get_template_directory_uri() . '/css/cart.css');
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');

// Register menus
function my_theme_menus()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my_theme'),
        'footer' => __('Footer Menu', 'my_theme'),
    ));
}
add_action('init', 'my_theme_menus');

function enqueue_bootstrap()
{
    // Replace this URL with the URL of your Bootstrap CSS file
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    // Replace this URL with the URL of your Bootstrap JS file
    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

function custom_rewrite_rule()
{
    add_rewrite_rule('^product/([^/]*)/?', 'index.php?pagename=product&id=$matches[1]', 'top');
    add_rewrite_rule('^checkout', 'index.php?pagename=checkout', 'top');
    add_rewrite_rule('^orders', 'index.php?pagename=orders', 'top');
    add_rewrite_rule('^order/([^/]*)/?', 'index.php?pagename=order&id=$matches[1]', 'top');
}

add_action('init', 'custom_rewrite_rule', 10, 0);

function custom_query_vars($vars)
{
    $vars[] = 'id';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');
