<?php
/**
 * Restaurant Theme Functions
 * 
 * @package Restaurant_Theme
 * @author Faik Guler
 * @link https://www.faikguler.com
 */

if (!defined('ABSPATH')) exit;

// Define theme constants
$theme_data = wp_get_theme();
define('RESTAURANT_THEME_VERSION', $theme_data->get('Version'));
define('RESTAURANT_THEME_DIR', get_template_directory());
define('RESTAURANT_THEME_URI', get_template_directory_uri());

// Include all modular files
require_once RESTAURANT_THEME_DIR . '/inc/theme-setup.php';
require_once RESTAURANT_THEME_DIR . '/inc/custom-post-types.php';
require_once RESTAURANT_THEME_DIR . '/inc/customizer-settings.php';
require_once RESTAURANT_THEME_DIR . '/inc/ajax-handlers.php';
require_once RESTAURANT_THEME_DIR . '/inc/meta-boxes.php';
require_once RESTAURANT_THEME_DIR . '/inc/shortcodes.php';
require_once RESTAURANT_THEME_DIR . '/inc/admin-functions.php';
require_once RESTAURANT_THEME_DIR . '/inc/demo-data.php';

// Include GitHub updater
require_once RESTAURANT_THEME_DIR . '/inc/github-updater.php';

// Initialize theme
add_action('after_setup_theme', 'restaurant_theme_init');

function restaurant_theme_init() {
    do_action('restaurant_theme_loaded');
}

// Localize script for AJAX
function restaurant_theme_localize_script() {
    wp_localize_script('restaurant-theme-script', 'restaurantTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('restaurant_theme_nonce'),
        'homeUrl' => home_url('/')
    ));
}
add_action('wp_enqueue_scripts', 'restaurant_theme_localize_script');