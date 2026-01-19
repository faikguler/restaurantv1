<?php
/**
 * Customizer Settings
 */

if (!defined('ABSPATH')) exit;

// Customizer Settings
function restaurant_theme_customize_register($wp_customize) {
    
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title' => __('Hero Section', 'restaurant-theme'),
        'priority' => 30
    ));
    
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Authentic Turkish Cuisine <span>in Warrington</span>',
        'sanitize_callback' => 'wp_kses_post'
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'restaurant-theme'),
        'section' => 'hero_section',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Experience delicious Turkish flavours, charcoal grills, freshly made meze and warm hospitality in a modern setting.',
        'sanitize_callback' => 'sanitize_textarea_field'
    ));
    
    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'restaurant-theme'),
        'section' => 'hero_section',
        'type' => 'textarea'
    ));
    
    $wp_customize->add_setting('hero_background', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_background', array(
        'label' => __('Hero Background Image', 'restaurant-theme'),
        'section' => 'hero_section'
    )));
    
    // About Section
    $wp_customize->add_section('about_section', array(
        'title' => __('About Section', 'restaurant-theme'),
        'priority' => 35
    ));
    
    $wp_customize->add_setting('about_title', array(
        'default' => 'About Us',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('about_title', array(
        'label' => __('About Title', 'restaurant-theme'),
        'section' => 'about_section',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('about_content', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));
    
    $wp_customize->add_control('about_content', array(
        'label' => __('About Content', 'restaurant-theme'),
        'section' => 'about_section',
        'type' => 'textarea'
    ));
    
    $wp_customize->add_setting('about_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'about_image', array(
        'label' => __('About Image', 'restaurant-theme'),
        'section' => 'about_section'
    )));
    
    // Philosophy Section
    $wp_customize->add_section('philosophy_section', array(
        'title' => __('Philosophy Section', 'restaurant-theme'),
        'priority' => 36
    ));
    
    $wp_customize->add_setting('philosophy_title', array(
        'default' => 'Our Philosophy',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('philosophy_title', array(
        'label' => __('Philosophy Title', 'restaurant-theme'),
        'section' => 'philosophy_section',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('philosophy_subtitle', array(
        'default' => 'We believe great food begins with passion and ends with people.',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('philosophy_subtitle', array(
        'label' => __('Philosophy Subtitle', 'restaurant-theme'),
        'section' => 'philosophy_section',
        'type' => 'text'
    ));
    
    // Contact Information
    $wp_customize->add_section('contact_info', array(
        'title' => __('Contact Information', 'restaurant-theme'),
        'priority' => 40
    ));
    
    $wp_customize->add_setting('phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('phone', array(
        'label' => __('Phone Number', 'restaurant-theme'),
        'section' => 'contact_info',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email'
    ));
    
    $wp_customize->add_control('email', array(
        'label' => __('Email Address', 'restaurant-theme'),
        'section' => 'contact_info',
        'type' => 'email'
    ));
    
    $wp_customize->add_setting('address', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field'
    ));
    
    $wp_customize->add_control('address', array(
        'label' => __('Address', 'restaurant-theme'),
        'section' => 'contact_info',
        'type' => 'textarea'
    ));
    
    $wp_customize->add_setting('opening_hours', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field'
    ));
    
    $wp_customize->add_control('opening_hours', array(
        'label' => __('Opening Hours', 'restaurant-theme'),
        'section' => 'contact_info',
        'type' => 'textarea'
    ));
    
    // Special Offer Section
    $wp_customize->add_section('special_offer', array(
        'title' => __('Special Offer Section', 'restaurant-theme'),
        'priority' => 45
    ));
    
    $wp_customize->add_setting('special_offer_active', array(
        'default' => true,
        'sanitize_callback' => 'restaurant_sanitize_boolean'
    ));
    
    $wp_customize->add_control('special_offer_active', array(
        'label' => __('Show Special Offer Section', 'restaurant-theme'),
        'section' => 'special_offer',
        'type' => 'checkbox'
    ));
    
    $wp_customize->add_setting('offer_title', array(
        'default' => 'Good Times, Great Drinks',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('offer_title', array(
        'label' => __('Offer Title', 'restaurant-theme'),
        'section' => 'special_offer',
        'type' => 'text'
    ));
    
    $wp_customize->add_setting('offer_description', array(
        'default' => 'Enjoy our special promotion<br><span class="highlight">Buy 1 Get 1 Free</span><br>On all Drinks<br><strong>Friday & Saturday | 10PM – 12AM</strong>',
        'sanitize_callback' => 'wp_kses_post'
    ));
    
    $wp_customize->add_control('offer_description', array(
        'label' => __('Offer Description', 'restaurant-theme'),
        'section' => 'special_offer',
        'type' => 'textarea',
        'description' => __('Use HTML for formatting: &lt;br&gt; for line breaks, &lt;span class="highlight"&gt; for highlighted text', 'restaurant-theme')
    ));
    
    // Layout Settings
    $wp_customize->add_section('layout_settings', array(
        'title' => __('Layout Settings', 'restaurant-theme'),
        'priority' => 25
    ));
    
    $wp_customize->add_setting('single_page_mode', array(
        'default' => true,
        'sanitize_callback' => 'restaurant_sanitize_boolean'
    ));
    
    $wp_customize->add_control('single_page_mode', array(
        'label' => __('Enable Single Page Mode', 'restaurant-theme'),
        'description' => __('When enabled, all content displays on one scrollable page. When disabled, separate pages are used.', 'restaurant-theme'),
        'section' => 'layout_settings',
        'type' => 'checkbox'
    ));
    
    // Social Media
    $wp_customize->add_section('social_media', array(
        'title' => __('Social Media', 'restaurant-theme'),
        'priority' => 50
    ));
    
    $wp_customize->add_setting('facebook_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('facebook_url', array(
        'label' => __('Facebook URL', 'restaurant-theme'),
        'section' => 'social_media',
        'type' => 'url'
    ));
    
    $wp_customize->add_setting('instagram_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('instagram_url', array(
        'label' => __('Instagram URL', 'restaurant-theme'),
        'section' => 'social_media',
        'type' => 'url'
    ));
    
    $wp_customize->add_setting('twitter_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    $wp_customize->add_control('twitter_url', array(
        'label' => __('Twitter URL', 'restaurant-theme'),
        'section' => 'social_media',
        'type' => 'url'
    ));
    
    $wp_customize->add_setting('map_embed', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));
    
    $wp_customize->add_control('map_embed', array(
        'label' => __('Google Maps Embed Code', 'restaurant-theme'),
        'section' => 'contact_info',
        'type' => 'textarea',
        'description' => __('Paste your Google Maps iframe embed code here', 'restaurant-theme')
    ));
        
    // Multi-page settings - info
    $wp_customize->add_setting('multi_page_info', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));

    $wp_customize->add_control('multi_page_info', array(
        'label' => __('Multi-Page Mode Setup', 'restaurant-theme'),
        'description' => sprintf(
            __('<p>To setup multi-page mode, please <a href="%s" target="_blank">click here to create pages automatically</a>.</p><p>After creating pages, go to <a href="%s" target="_blank">Appearance > Menus</a> to create a menu and assign it as Primary Menu.</p>', 'restaurant-theme'),
            admin_url('admin-ajax.php?action=restaurant_create_pages&nonce=' . wp_create_nonce('restaurant_create_pages')),
            admin_url('nav-menus.php')
        ),
        'section' => 'layout_settings',
        'type' => 'hidden',
        'priority' => 10
    ));
}
add_action('customize_register', 'restaurant_theme_customize_register');