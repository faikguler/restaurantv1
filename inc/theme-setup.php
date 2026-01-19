<?php
/**
 * Theme Setup & Configuration
 */

if (!defined('ABSPATH')) exit;

// Include custom walker class
require_once get_template_directory() . '/inc/class-restaurant-theme-walker-nav-menu.php';

// Theme Setup
function restaurant_theme_setup() {
    // Add default posts and comments RSS feed links
    add_theme_support('automatic-feed-links');
    
    // Title tag support
    add_theme_support('title-tag');
    
    // Featured image support
    add_theme_support('post-thumbnails');
    add_image_size('menu-item', 500, 350, true);
    add_image_size('about-image', 800, 600, true);
    
    // Navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'restaurant-theme'),
        'footer' => __('Footer Menu', 'restaurant-theme')
    ));
    
    // HTML5 support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
    
    // Custom logo
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 100,
        'flex-height' => true,
        'flex-width' => true
    ));
    
    // Add support for custom page templates
    add_theme_support('custom-page-templates');
}
add_action('after_setup_theme', 'restaurant_theme_setup');

// Enqueue Scripts and Styles
function restaurant_theme_scripts() {
    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    
    // Main stylesheet (sadece tema bilgileri için, stiller main.css'de)
    wp_enqueue_style('restaurant-theme-style', get_stylesheet_uri(), array(), RESTAURANT_THEME_VERSION);
    
    // Main CSS (tüm stiller burada)
    wp_enqueue_style('restaurant-theme-main', get_template_directory_uri() . '/css/main.css', array(), RESTAURANT_THEME_VERSION);
    
    // Responsive CSS (main.css'den sonra yüklenecek)
    wp_enqueue_style('restaurant-theme-responsive', get_template_directory_uri() . '/css/responsive.css', array('restaurant-theme-main'), RESTAURANT_THEME_VERSION);
    
    // Admin CSS (sadece admin panelinde)
    if (is_admin()) {
        wp_enqueue_style('restaurant-theme-admin', get_template_directory_uri() . '/css/admin.css', array(), RESTAURANT_THEME_VERSION);
    }
    
    // Main JavaScript
    wp_enqueue_script('restaurant-theme-script', get_template_directory_uri() . '/js/main.js', array('jquery'), RESTAURANT_THEME_VERSION, true);
    
    // Localize script for AJAX
    wp_localize_script('restaurant-theme-script', 'restaurantTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('restaurant_theme_nonce'),
        'homeUrl' => home_url('/')
    ));
}
add_action('wp_enqueue_scripts', 'restaurant_theme_scripts');

// Register Widget Areas
function restaurant_theme_widgets_init() {
    register_sidebar(array(
        'name' => __('Footer Widget Area 1', 'restaurant-theme'),
        'id' => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ));
}
add_action('widgets_init', 'restaurant_theme_widgets_init');

// Sanitize boolean
function restaurant_sanitize_boolean($input) {
    return (bool) $input;
}

// Body class filter
function restaurant_theme_body_class_filter($classes) {
    // Add theme-specific class
    $classes[] = 'restaurant-theme';
    
    // Add mode class
    if (!get_theme_mod('single_page_mode', true)) {
        $classes[] = 'multi-page-mode';
    } else {
        $classes[] = 'single-page-mode';
    }
    
    return $classes;
}
add_filter('body_class', 'restaurant_theme_body_class_filter');

// Flush rewrite rules on theme activation
function restaurant_theme_activation() {
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'restaurant_theme_activation');