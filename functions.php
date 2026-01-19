<?php
/**
 * Restaurant Theme v1 Functions
 * 
 * @package Restaurant_Theme
 * @author Faik Guler
 * @link https://www.faikguler.com
 */

if (!defined('ABSPATH')) exit;

// Define theme constants
define('RESTAURANT_THEME_VERSION', '1.0.0');
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

// Include GitHub updater (comment out if not needed immediately)
require_once RESTAURANT_THEME_DIR . '/inc/github-updater.php';

// Include license manager (for future premium features)
// require_once RESTAURANT_THEME_DIR . '/inc/license-manager.php';

// Initialize theme
add_action('after_setup_theme', 'restaurant_theme_init');

function restaurant_theme_init() {
    // Theme initialization hook
    do_action('restaurant_theme_loaded');
}