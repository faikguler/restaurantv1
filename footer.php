<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-about">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
                    <i class="fas fa-utensils"></i> <?php bloginfo('name'); ?>
                </a>
                <p><?php bloginfo('description'); ?></p>
                <div class="social-icons">
                    <?php if (get_theme_mod('facebook_url')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('facebook_url')); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('instagram_url')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('instagram_url')); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('twitter_url')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('twitter_url')); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-links">
                <h4><?php _e('Quick Links', 'restaurant-theme'); ?></h4>
                <?php
                if (get_theme_mod('single_page_mode', true)) {
                    ?>
                    <ul>
                        <li><a href="#home"><i class="fas fa-chevron-right"></i> <?php _e('Home', 'restaurant-theme'); ?></a></li>
                        <li><a href="#about"><i class="fas fa-chevron-right"></i> <?php _e('About', 'restaurant-theme'); ?></a></li>
                        <li><a href="#philosophy"><i class="fas fa-chevron-right"></i> <?php _e('Philosophy', 'restaurant-theme'); ?></a></li>
                        <li><a href="#menu"><i class="fas fa-chevron-right"></i> <?php _e('Menu', 'restaurant-theme'); ?></a></li>
                        <li><a href="#reservation"><i class="fas fa-chevron-right"></i> <?php _e('Reservation', 'restaurant-theme'); ?></a></li>
                        <li><a href="#contact"><i class="fas fa-chevron-right"></i> <?php _e('Contact', 'restaurant-theme'); ?></a></li>
                    </ul>
                    <?php
                } else {
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container' => false,
                        'fallback_cb' => false
                    ));
                }
                ?>
            </div>
            
            <div class="footer-contact">
                <h4><?php _e('Contact Info', 'restaurant-theme'); ?></h4>
                <?php if (get_theme_mod('address')): ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo wp_kses_post(nl2br(get_theme_mod('address'))); ?></p>
                <?php endif; ?>
                <?php if (get_theme_mod('phone')): ?>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('phone'))); ?>"><?php echo esc_html(get_theme_mod('phone')); ?></a></p>
                <?php endif; ?>
                <?php if (get_theme_mod('email')): ?>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo esc_attr(get_theme_mod('email')); ?>"><?php echo esc_html(get_theme_mod('email')); ?></a></p>
                <?php endif; ?>
                <?php if (get_theme_mod('opening_hours')): ?>
                    <p><i class="fas fa-clock"></i> <?php echo wp_kses_post(nl2br(get_theme_mod('opening_hours'))); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'restaurant-theme'); ?></p>
            <p><?php _e('Thank you for choosing', 'restaurant-theme'); ?> <?php bloginfo('name'); ?>. <?php _e('We look forward to welcoming you.', 'restaurant-theme'); ?></p>
            <div class="developer-credit">
                <?php _e('WordPress Theme developed by', 'restaurant-theme'); ?> 
                <a href="https://www.faikguler.com" target="_blank" rel="nofollow noopener">www.faikguler.com</a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>