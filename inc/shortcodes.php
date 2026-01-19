<?php
/**
 * Shortcodes
 */

if (!defined('ABSPATH')) exit;

// Section shortcodes for multi-page mode
function restaurant_about_section_shortcode() {
    ob_start();
    ?>
    <section class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2><?php echo esc_html(get_theme_mod('about_title', 'About Us')); ?></h2>
                    <?php if (get_theme_mod('about_content')): ?>
                        <?php echo wp_kses_post(wpautop(get_theme_mod('about_content'))); ?>
                    <?php else: ?>
                        <p><?php _e('Cappadocia Restaurant is inspired by the rich culinary heritage of Turkey\'s most magical region — a place known for its warm hospitality, ancient flavours and unforgettable landscapes.', 'restaurant-theme'); ?></p>
                        <p><?php _e('Every dish we serve is freshly prepared using quality ingredients, traditional recipes and modern presentation.', 'restaurant-theme'); ?></p>
                    <?php endif; ?>
                    <?php 
                    if (get_theme_mod('single_page_mode', true)) {
                        $reservation_link = '#reservation';
                    } else {
                        $reservation_page = get_page_by_title('Reservation');
                        $reservation_link = $reservation_page ? get_permalink($reservation_page->ID) : '#';
                    }
                    ?>
                    <a href="<?php echo esc_url($reservation_link); ?>" class="reservation-btn" style="margin-top: 20px;">
                        <i class="fas fa-table"></i> <?php _e('Make a Reservation', 'restaurant-theme'); ?>
                    </a>
                </div>
                <div class="about-image">
                    <?php if (get_theme_mod('about_image')): ?>
                        <img src="<?php echo esc_url(get_theme_mod('about_image')); ?>" alt="<?php bloginfo('name'); ?>">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=687&q=80" alt="<?php bloginfo('name'); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_about_section', 'restaurant_about_section_shortcode');

function restaurant_menu_section_shortcode() {
    ob_start();
    ?>
    <section class="menu">
        <div class="container">
            <div class="section-title">
                <h2><?php _e('Our Menu', 'restaurant-theme'); ?></h2>
                <p class="section-subtitle"><?php _e('Discover authentic Turkish flavours with our diverse selection of dishes', 'restaurant-theme'); ?></p>
            </div>
            
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'menu_category',
                'hide_empty' => true
            ));
            
            if (!empty($categories) && !is_wp_error($categories)):
            ?>
            <div class="menu-tabs">
                <?php 
                $first = true;
                foreach ($categories as $category): 
                ?>
                <button class="tab-btn <?php echo $first ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category->slug); ?>">
                    <i class="fas fa-utensils"></i> <?php echo esc_html($category->name); ?>
                </button>
                <?php 
                $first = false;
                endforeach; 
                ?>
            </div>
            
            <?php
            $first = true;
            foreach ($categories as $category):
                $menu_items = new WP_Query(array(
                    'post_type' => 'menu_item',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'menu_category',
                            'field' => 'slug',
                            'terms' => $category->slug
                        )
                    )
                ));
                
                if ($menu_items->have_posts()):
            ?>
            <div class="menu-category <?php echo $first ? 'active' : ''; ?>" id="<?php echo esc_attr($category->slug); ?>">
                <div class="category-header">
                    <h3><?php echo esc_html(strtoupper($category->name)); ?></h3>
                    <?php if ($category->description): ?>
                    <p><?php echo esc_html($category->description); ?></p>
                    <?php endif; ?>
                </div>
                <div class="menu-items">
                    <?php while ($menu_items->have_posts()): $menu_items->the_post(); 
                        $price = get_post_meta(get_the_ID(), '_menu_item_price', true);
                        $price_alt = get_post_meta(get_the_ID(), '_menu_item_price_alt', true);
                        $dietary = get_post_meta(get_the_ID(), '_menu_item_dietary', true);
                    ?>
                    <div class="menu-item">
                        <div class="menu-item-title"><?php the_title(); ?></div>
                        <?php if (has_post_thumbnail()): ?>
                        <div class="menu-item-image">
                            <?php the_post_thumbnail('menu-item'); ?>
                        </div>
                        <?php endif; ?>
                        <div class="menu-item-desc">
                            <?php the_content(); ?>
                            <?php if ($dietary): ?>
                                <span class="dietary"><?php echo esc_html($dietary); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="menu-item-price">
                            <?php echo esc_html($price); ?>
                            <?php if ($price_alt): ?>
                                / <?php echo esc_html($price_alt); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
                endif;
                wp_reset_postdata();
                $first = false;
            endforeach;
            ?>
            
            <?php else: ?>
            <p style="text-align: center;"><?php _e('No menu items found. Please add menu items from the WordPress admin.', 'restaurant-theme'); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_menu_section', 'restaurant_menu_section_shortcode');

function restaurant_reservation_section_shortcode() {
    ob_start();
    ?>
    <section class="reservation">
        <div class="container">
            <div class="reservation-container">
                <div class="reservation-info">
                    <h2><?php _e('Book Your', 'restaurant-theme'); ?> <span><?php _e('Table', 'restaurant-theme'); ?></span></h2>
                    <p><?php _e('Secure your spot for an unforgettable dining experience. Whether it\'s a romantic dinner, family gathering, or special celebration, we\'ll ensure your visit is memorable.', 'restaurant-theme'); ?></p>
                    
                    <div class="reservation-details">
                        <?php if (get_theme_mod('phone')): ?>
                        <div class="reservation-detail">
                            <i class="fas fa-phone-alt"></i>
                            <h4><?php _e('Call Us', 'restaurant-theme'); ?></h4>
                            <p><?php echo esc_html(get_theme_mod('phone')); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (get_theme_mod('email')): ?>
                        <div class="reservation-detail">
                            <i class="fas fa-envelope"></i>
                            <h4><?php _e('Email', 'restaurant-theme'); ?></h4>
                            <p><?php echo esc_html(get_theme_mod('email')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="reservation-form-container">
                    <h3><?php _e('Make a Reservation', 'restaurant-theme'); ?></h3>
                    <form id="reservationForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><?php _e('Full Name', 'restaurant-theme'); ?> *</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="<?php esc_attr_e('Your name', 'restaurant-theme'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email"><?php _e('Email Address', 'restaurant-theme'); ?> *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="<?php esc_attr_e('Your email', 'restaurant-theme'); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone"><?php _e('Phone Number', 'restaurant-theme'); ?> *</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="<?php esc_attr_e('Your phone number', 'restaurant-theme'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="guests"><?php _e('Number of Guests', 'restaurant-theme'); ?> *</label>
                                <select id="guests" name="guests" class="form-control" required>
                                    <option value="" disabled selected><?php _e('Select guests', 'restaurant-theme'); ?></option>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i == 1 ? __('Person', 'restaurant-theme') : __('People', 'restaurant-theme'); ?></option>
                                    <?php endfor; ?>
                                    <option value="9+"><?php _e('9+ People', 'restaurant-theme'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date"><?php _e('Date', 'restaurant-theme'); ?> *</label>
                                <input type="date" id="date" name="date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="time"><?php _e('Time', 'restaurant-theme'); ?> *</label>
                                <select id="time" name="time" class="form-control" required>
                                    <option value="" disabled selected><?php _e('Select time', 'restaurant-theme'); ?></option>
                                    <option value="17:00">5:00 PM</option>
                                    <option value="17:30">5:30 PM</option>
                                    <option value="18:00">6:00 PM</option>
                                    <option value="18:30">6:30 PM</option>
                                    <option value="19:00">7:00 PM</option>
                                    <option value="19:30">7:30 PM</option>
                                    <option value="20:00">8:00 PM</option>
                                    <option value="20:30">8:30 PM</option>
                                    <option value="21:00">9:00 PM</option>
                                    <option value="21:30">9:30 PM</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><?php _e('Special Requests (Optional)', 'restaurant-theme'); ?></label>
                            <textarea id="message" name="message" class="form-control" placeholder="<?php esc_attr_e('Any special requests or dietary requirements?', 'restaurant-theme'); ?>" rows="4"></textarea>
                        </div>
                        
                        <button type="submit" class="form-submit-btn">
                            <i class="fas fa-paper-plane"></i> <?php _e('Submit Reservation', 'restaurant-theme'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_reservation_section', 'restaurant_reservation_section_shortcode');

function restaurant_contact_section_shortcode() {
    ob_start();
    ?>
    <section class="contact">
        <div class="container">
            <div class="section-title">
                <h2><?php _e('Contact Us', 'restaurant-theme'); ?></h2>
                <p class="section-subtitle"><?php _e('We\'re here to make your dining experience exceptional', 'restaurant-theme'); ?></p>
            </div>
            
            <div class="contact-container">
                <div class="contact-info">
                    <h3><?php _e('Get in Touch', 'restaurant-theme'); ?></h3>
                    
                    <?php if (get_theme_mod('address')): ?>
                    <div class="contact-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4><?php _e('Address', 'restaurant-theme'); ?></h4>
                            <p><?php echo wp_kses_post(nl2br(get_theme_mod('address'))); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('phone')): ?>
                    <div class="contact-detail">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4><?php _e('Phone', 'restaurant-theme'); ?></h4>
                            <p><a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('phone'))); ?>"><?php echo esc_html(get_theme_mod('phone')); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('email')): ?>
                    <div class="contact-detail">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4><?php _e('Email', 'restaurant-theme'); ?></h4>
                            <p><a href="mailto:<?php echo esc_attr(get_theme_mod('email')); ?>"><?php echo esc_html(get_theme_mod('email')); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('opening_hours')): ?>
                    <div class="opening-hours">
                        <h4><i class="fas fa-clock"></i> <?php _e('Opening Hours', 'restaurant-theme'); ?></h4>
                        <div><?php echo wp_kses_post(nl2br(get_theme_mod('opening_hours'))); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('map_embed')): ?>
                    <div class="map-container">
                        <?php echo get_theme_mod('map_embed'); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="contact-form-container">
                    <h3><?php _e('Leave us a Message', 'restaurant-theme'); ?></h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="contact-name"><?php _e('Name', 'restaurant-theme'); ?> *</label>
                            <input type="text" id="contact-name" name="name" class="form-control" placeholder="<?php esc_attr_e('Your name', 'restaurant-theme'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email"><?php _e('Email', 'restaurant-theme'); ?> *</label>
                            <input type="email" id="contact-email" name="email" class="form-control" placeholder="<?php esc_attr_e('Your email', 'restaurant-theme'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-phone"><?php _e('Phone Number', 'restaurant-theme'); ?></label>
                            <input type="tel" id="contact-phone" name="phone" class="form-control" placeholder="<?php esc_attr_e('Your phone number', 'restaurant-theme'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="contact-method"><?php _e('Preferred Contact Method', 'restaurant-theme'); ?></label>
                            <select id="contact-method" name="method" class="form-control">
                                <option value="" disabled selected><?php _e('Select method', 'restaurant-theme'); ?></option>
                                <option value="email"><?php _e('Email', 'restaurant-theme'); ?></option>
                                <option value="phone"><?php _e('Phone', 'restaurant-theme'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contact-message"><?php _e('Message', 'restaurant-theme'); ?> *</label>
                            <textarea id="contact-message" name="message" class="form-control" placeholder="<?php esc_attr_e('Your message', 'restaurant-theme'); ?>" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="form-submit-btn">
                            <i class="fas fa-paper-plane"></i> <?php _e('Send Message', 'restaurant-theme'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_contact_section', 'restaurant_contact_section_shortcode');

function restaurant_philosophy_section_shortcode() {
    ob_start();
    ?>
    <section class="philosophy">
        <div class="container">
            <div class="philosophy-content">
                <div class="section-title">
                    <h2><?php echo esc_html(get_theme_mod('philosophy_title', 'Our Philosophy')); ?></h2>
                    <p class="section-subtitle"><?php echo esc_html(get_theme_mod('philosophy_subtitle', 'We believe great food begins with passion and ends with people.')); ?></p>
                </div>
                
                <div class="features">
                    <?php
                    // Get features from customizer or use defaults
                    $features = array(
                        array(
                            'icon' => 'fa-fire',
                            'title' => __('Charcoal Grill Expertise', 'restaurant-theme'),
                            'description' => __('Recipes passed down through generations, prepared with respect for tradition.', 'restaurant-theme')
                        ),
                        array(
                            'icon' => 'fa-seedling',
                            'title' => __('Fresh Meze Selection', 'restaurant-theme'),
                            'description' => __('Vibrant small plates ideal for sharing with family and friends.', 'restaurant-theme')
                        ),
                        array(
                            'icon' => 'fa-award',
                            'title' => __('Quality Ingredients', 'restaurant-theme'),
                            'description' => __('We carefully select the finest produce, meats and spices for every dish.', 'restaurant-theme')
                        ),
                        array(
                            'icon' => 'fa-heart',
                            'title' => __('Warm & Welcoming', 'restaurant-theme'),
                            'description' => __('We bring genuine Turkish hospitality — friendly, personal and caring.', 'restaurant-theme')
                        )
                    );
                    
                    foreach ($features as $feature):
                    ?>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas <?php echo esc_attr($feature['icon']); ?>"></i>
                        </div>
                        <h3><?php echo esc_html($feature['title']); ?></h3>
                        <p><?php echo esc_html($feature['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                if (get_theme_mod('single_page_mode', true)) {
                    $reservation_link = '#reservation';
                } else {
                    $reservation_page = get_page_by_title('Reservation');
                    $reservation_link = $reservation_page ? get_permalink($reservation_page->ID) : '#';
                }
                ?>
                <a href="<?php echo esc_url($reservation_link); ?>" class="reservation-btn" style="margin-top: 50px;">
                    <i class="fas fa-calendar-check"></i> <?php _e('Reserve a Table', 'restaurant-theme'); ?>
                </a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_philosophy_section', 'restaurant_philosophy_section_shortcode');

function restaurant_testimonials_section_shortcode() {
    ob_start();
    ?>
    <section class="testimonials">
        <div class="container">
            <div class="section-title">
                <h2><?php _e('What Our Guests Are Saying', 'restaurant-theme'); ?></h2>
                <p class="section-subtitle"><?php _e('Our guests don\'t just come for the food — they come for the flavor, the fire, and the feeling of home.', 'restaurant-theme'); ?></p>
            </div>
            
            <div class="testimonial-slider">
                <?php
                $testimonials = new WP_Query(array(
                    'post_type' => 'testimonial',
                    'posts_per_page' => 6,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($testimonials->have_posts()):
                    while ($testimonials->have_posts()): $testimonials->the_post();
                        $author = get_post_meta(get_the_ID(), '_testimonial_author', true);
                ?>
                <div class="testimonial">
                    <div class="testimonial-text">
                        <?php the_content(); ?>
                    </div>
                    <div class="testimonial-author"><?php echo esc_html($author ? $author : get_the_title()); ?></div>
                </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                    // Default testimonials if none exist
                    $default_testimonials = array(
                        array(
                            'text' => 'Incredible meal! Exceptional food, superb service, and a lovely atmosphere. Will definitely be back soon. Highly recommend!',
                            'author' => 'Nihad B'
                        ),
                        array(
                            'text' => 'Very welcoming, lovely polite staff. Food is more than delicious, lots of variety of food and drinks.',
                            'author' => 'Emma R'
                        ),
                        array(
                            'text' => 'Everything is very delicious, the food service is fast, the employees are very attentive.',
                            'author' => 'Cemile C'
                        )
                    );
                    
                    foreach ($default_testimonials as $testimonial):
                ?>
                <div class="testimonial">
                    <div class="testimonial-text"><?php echo esc_html($testimonial['text']); ?></div>
                    <div class="testimonial-author"><?php echo esc_html($testimonial['author']); ?></div>
                </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_testimonials_section', 'restaurant_testimonials_section_shortcode');

function restaurant_special_offer_shortcode() {
    if (!get_theme_mod('special_offer_active', true) || !get_theme_mod('offer_title')) {
        return '';
    }
    
    ob_start();
    ?>
    <section class="special-offer">
        <div class="container">
            <div class="special-offer-content">
                <h2><?php echo esc_html(get_theme_mod('offer_title')); ?></h2>
                <?php if (get_theme_mod('offer_description')): ?>
                    <div class="offer-description">
                        <?php echo wp_kses_post(get_theme_mod('offer_description')); ?>
                    </div>
                <?php endif; ?>
                <?php
                if (get_theme_mod('single_page_mode', true)) {
                    $offer_link = '#reservation';
                } else {
                    $reservation_page = get_page_by_title('Reservation');
                    $offer_link = $reservation_page ? get_permalink($reservation_page->ID) : '#';
                }
                ?>
                <a href="<?php echo esc_url($offer_link); ?>" class="reservation-btn" style="margin-top: 30px;">
                    <i class="fas fa-glass-cheers"></i> <?php _e('Book Your Table', 'restaurant-theme'); ?>
                </a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('restaurant_special_offer', 'restaurant_special_offer_shortcode');

// Multi-page mode page templates
function restaurant_theme_page_templates($templates) {
    $templates['page-menu.php'] = __('Menu Page Template', 'restaurant-theme');
    $templates['page-contact.php'] = __('Contact Page Template', 'restaurant-theme');
    $templates['page-reservation.php'] = __('Reservation Page Template', 'restaurant-theme');
    return $templates;
}
add_filter('theme_page_templates', 'restaurant_theme_page_templates');

// Template include
function restaurant_theme_load_template($template) {
    if (is_page()) {
        $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
        
        if ($page_template && $page_template != 'default') {
            $new_template = locate_template(array($page_template));
            if ($new_template) {
                return $new_template;
            }
        }
    }
    return $template;
}
add_filter('template_include', 'restaurant_theme_load_template');

// Multi-page mode için menü düzenlemesi
function restaurant_theme_default_menu() {
    if (get_theme_mod('single_page_mode', true)) {
        return;
    }
    
    echo '<ul>';
    
    // Home link - always show
    $current_page_id = get_queried_object_id();
    $home_class = (is_front_page() || is_home()) ? 'class="current-menu-item"' : '';
    echo '<li ' . $home_class . '><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'restaurant-theme') . '</a></li>';
    
    // Anahtar sayfaları göster
    $pages_to_show = array('About', 'Menu', 'Reservation', 'Contact', 'Philosophy', 'Testimonials');
    
    foreach ($pages_to_show as $page_title) {
        $page = get_page_by_title($page_title);
        if ($page) {
            $current_class = ($page->ID == $current_page_id) ? 'class="current-menu-item"' : '';
            echo '<li ' . $current_class . '><a href="' . get_permalink($page->ID) . '">' . esc_html($page_title) . '</a></li>';
        } else {
            // Sayfa yoksa admin panelinde uyarı
            if (current_user_can('edit_theme_options')) {
                echo '<li><a href="#" style="color:#ff0000;"><i class="fas fa-exclamation-triangle"></i> ' . esc_html($page_title) . ' (Create Page)</a></li>';
            }
        }
    }
    
    // Diğer sayfaları da göster (opsiyonel)
    $other_pages = get_pages(array(
        'sort_column' => 'menu_order',
        'sort_order' => 'ASC',
        'exclude' => array(get_option('page_on_front'))
    ));
    
    foreach ($other_pages as $page) {
        // Sadece anahtar sayfalarda olmayanları ekle
        if (!in_array($page->post_title, $pages_to_show)) {
            $current_class = ($page->ID == $current_page_id) ? 'class="current-menu-item"' : '';
            echo '<li ' . $current_class . '><a href="' . get_permalink($page->ID) . '">' . esc_html($page->post_title) . '</a></li>';
        }
    }
    
    echo '</ul>';
}

// Breadcrumb fonksiyonu
function restaurant_theme_breadcrumbs() {
    if (get_theme_mod('single_page_mode', true) || is_front_page()) {
        return;
    }
    
    echo '<div class="breadcrumbs"><div class="container">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . __('Home', 'restaurant-theme') . '</a>';
    
    if (is_page() && !is_front_page()) {
        echo ' <span>&gt;</span> ';
        the_title();
    }
    
    echo '</div></div>';
}