<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(get_theme_mod('single_page_mode', true) ? '' : 'multi-page-mode'); ?>>
<?php wp_body_open(); ?>

<!-- Header & Navigation -->
<header id="header">
    <div class="header-container container">
        <?php if (has_custom_logo()): ?>
            <?php the_custom_logo(); ?>
        <?php else: ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                <i class="fas fa-utensils logo-icon"></i>
                <?php bloginfo('name'); ?>
            </a>
        <?php endif; ?>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <nav id="mainNav">
            <?php 
            if (get_theme_mod('single_page_mode', true)) {
                // Single Page Mode Menu - Anchor links
                ?>
                <ul>
                    <li><a href="#home"><?php _e('Home', 'restaurant-theme'); ?></a></li>
                    <li><a href="#about"><?php _e('About', 'restaurant-theme'); ?></a></li>
                    <li><a href="#philosophy"><?php _e('Philosophy', 'restaurant-theme'); ?></a></li>
                    <li><a href="#menu"><?php _e('Menu', 'restaurant-theme'); ?></a></li>
                    <li><a href="#testimonials"><?php _e('Reviews', 'restaurant-theme'); ?></a></li>
                    <li><a href="#reservation"><?php _e('Reservation', 'restaurant-theme'); ?></a></li>
                    <li><a href="#contact"><?php _e('Contact', 'restaurant-theme'); ?></a></li>
                </ul>
                <?php
            } else {
                // Multi Page Mode Menu - WordPress Pages
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => '',
                    'items_wrap' => '<ul>%3$s</ul>',
                    'fallback_cb' => 'restaurant_theme_default_menu',
                    'walker' => new Restaurant_Theme_Walker_Nav_Menu()
                ));
            }
            ?>
        </nav>
    </div>
</header>