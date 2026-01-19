<?php
/**
 * Demo Data Functions
 */

if (!defined('ABSPATH')) exit;

// Multi-page mode functions
// Create default pages on theme activation for multi-page mode
function restaurant_theme_create_default_pages($force = false) {
    // Force parametresi ekleyin
    if (!$force && get_option('restaurant_theme_default_pages_created')) {
        return array(); // Boş dizi döndür
    }
    
    $pages = array(
        'Home' => array(
            'content' => '',
            'template' => 'page.php'
        ),
        'About' => array(
            'content' => '[restaurant_about_section]',
            'template' => 'page.php'
        ),
        'Menu' => array(
            'content' => '[restaurant_menu_section]',
            'template' => 'page-menu.php'
        ),
        'Reservation' => array(
            'content' => '[restaurant_reservation_section]',
            'template' => 'page.php'
        ),
        'Contact' => array(
            'content' => '[restaurant_contact_section]',
            'template' => 'page-contact.php'
        ),
        'Philosophy' => array(
            'content' => '[restaurant_philosophy_section]',
            'template' => 'page.php'
        ),
        'Testimonials' => array(
            'content' => '[restaurant_testimonials_section]',
            'template' => 'page.php'
        )
    );
    
    $created_pages = array();
    
    foreach ($pages as $page_title => $page_data) {
        $page_check = get_page_by_title($page_title);
        
        if (!$page_check) {
            $page_id = wp_insert_post(array(
                'post_title'     => $page_title,
                'post_content'   => $page_data['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'page_template'  => $page_data['template']
            ));
            
            if ($page_id) {
                $created_pages[$page_title] = $page_id;
            }
        }
    }
    
    // Set Home as front page if not already set
    $home_page = get_page_by_title('Home');
    if ($home_page && !get_option('page_on_front')) {
        update_option('page_on_front', $home_page->ID);
        update_option('show_on_front', 'page');
    }
    
    // Create primary menu if doesn't exist
    if (!has_nav_menu('primary')) {
        $menu_name = 'Primary Menu';
        $menu_exists = wp_get_nav_menu_object($menu_name);
        
        if (!$menu_exists) {
            $menu_id = wp_create_nav_menu($menu_name);
            
            // Add pages to menu
            foreach ($pages as $page_title => $page_data) {
                $page = get_page_by_title($page_title);
                if ($page) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'     => $page_title,
                        'menu-item-object'    => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type'      => 'post_type',
                        'menu-item-status'    => 'publish'
                    ));
                }
            }
            
            // Set menu location
            $locations = get_theme_mod('nav_menu_locations');
            if (empty($locations)) {
                $locations = array();
            }
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    update_option('restaurant_theme_default_pages_created', true);
    
    return $created_pages;
}
add_action('after_setup_theme', 'restaurant_theme_create_default_pages');

// Demo menu items oluşturma fonksiyonu
function restaurant_create_demo_menu_items() {
    // Demo menü kategorileri
    $categories = array(
        'starters' => array(
            'name' => 'Starters',
            'description' => 'Perfect beginnings to your Turkish feast'
        ),
        'grill' => array(
            'name' => 'Grill & Steak',
            'description' => 'Flame-grilled perfection from our charcoal oven'
        ),
        'specials' => array(
            'name' => 'Specials',
            'description' => 'Chef\'s special creations'
        ),
        'vegetarian' => array(
            'name' => 'Vegetarian',
            'description' => 'Delicious plant-based options'
        ),
        'drinks' => array(
            'name' => 'Drinks',
            'description' => 'Refreshments to complement your meal'
        ),
        'kids' => array(
            'name' => 'Kids Menu',
            'description' => 'All kids meals £7.50 per child'
        )
    );

    foreach ($categories as $slug => $cat_data) {
        if (!term_exists($cat_data['name'], 'menu_category')) {
            wp_insert_term($cat_data['name'], 'menu_category', array(
                'description' => $cat_data['description'],
                'slug' => $slug
            ));
        }
    }

    // Demo menü öğeleri
    $menu_items = array(
        array(
            'title' => 'Starter Sharing Platter',
            'content' => 'Hummus, Cacik, Sucuk Izgara, Peynirli Borek, Chicken Wings (for two people / for four people)',
            'price' => '£15.95 / £26.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Home-made soup of the day',
            'content' => 'Served with bread.',
            'price' => '£6.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Hummus',
            'content' => 'Blended chickpeas, tahini, olive oil, lemon & garlic. Served with pitta.',
            'price' => '£6.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Feta Cheese & Olives',
            'content' => 'Traditional feta cheese served with olives.',
            'price' => '£6.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Falafel',
            'content' => 'Mediterranean chickpea falafel with tahini sauce.',
            'price' => '£7.00',
            'category' => 'starters'
        ),
        array(
            'title' => 'Peynirli Börek',
            'content' => 'Filo pastry stuffed with feta & parsley.',
            'price' => '£7.50',
            'category' => 'starters'
        ),
        array(
            'title' => 'Buffalo Chicken Wings',
            'content' => 'Served with BBQ sauce.',
            'price' => '£7.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Sucuk Izgara',
            'content' => 'Grilled Turkish Sausage',
            'price' => '£7.95',
            'category' => 'starters'
        ),
        array(
            'title' => 'Chicken Kebab',
            'content' => 'Tender chicken pieces marinated in traditional spices, grilled to perfection.',
            'price' => '£18.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Lamb Kebab',
            'content' => 'Succulent lamb pieces marinated with herbs and spices.',
            'price' => '£21.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Mixed Kebab',
            'content' => 'A combination of chicken and lamb kebabs.',
            'price' => '£22.50',
            'category' => 'grill'
        ),
        array(
            'title' => 'Lamb Chops',
            'content' => 'Tender lamb chops marinated with herbs.',
            'price' => '£22.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Lamb Shank',
            'content' => 'Slow-cooked lamb shank served with rice.',
            'price' => '£21.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Chicken Wings Grill',
            'content' => 'Grilled chicken wings with special sauce.',
            'price' => '£17.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Adana Kebab',
            'content' => 'Spicy minced meat kebab.',
            'price' => '£17.95',
            'category' => 'grill'
        ),
        array(
            'title' => '10oz Sirloin Steak',
            'content' => 'Served with chips, mushrooms, asparagus, peppers & cherry tomatoes. Peppercorn or onion gravy + £1.95',
            'price' => '£23.95',
            'category' => 'grill'
        ),
        array(
            'title' => 'Chicken Mozzarella Asparagus',
            'content' => 'Grilled chicken with melted mozzarella and fresh asparagus.',
            'price' => '£17.95',
            'category' => 'specials'
        ),
        array(
            'title' => 'Moussaka',
            'content' => 'Traditional Turkish moussaka with layers of eggplant, potatoes, and minced meat.',
            'price' => '£17.95',
            'category' => 'specials'
        ),
        array(
            'title' => 'Beyti Kebab',
            'content' => 'Minced meat kebab wrapped in lavash bread, served with yogurt and tomato sauce.',
            'price' => '£21.95',
            'category' => 'specials'
        ),
        array(
            'title' => 'Halloumi & Goat\'s Cheese Burger',
            'content' => 'Grilled halloumi and goat\'s cheese served in a brioche bun with salad.',
            'price' => '£16.95',
            'category' => 'vegetarian'
        ),
        array(
            'title' => 'Vegetable Moussaka',
            'content' => 'Vegetarian version of the classic moussaka with eggplant, potatoes, and vegetables.',
            'price' => '£15.95',
            'category' => 'vegetarian'
        ),
        array(
            'title' => 'Peroni 330ml',
            'content' => 'Crisp and refreshing Italian lager',
            'price' => '£4.45',
            'category' => 'drinks'
        ),
        array(
            'title' => 'Corona 330ml',
            'content' => 'Light Mexican beer with lime',
            'price' => '£4.45',
            'category' => 'drinks'
        ),
        array(
            'title' => 'Guinness',
            'content' => 'Rich, dark Irish stout',
            'price' => '£5.25',
            'category' => 'drinks'
        ),
        array(
            'title' => 'Chicken Nuggets & Chips',
            'content' => 'Served with choice of ketchup or BBQ sauce',
            'price' => '£7.50',
            'category' => 'kids'
        ),
        array(
            'title' => 'Kids\' Margherita Pizza & Chips',
            'content' => 'A smaller version of our classic Margherita pizza',
            'price' => '£7.50',
            'category' => 'kids'
        ),
        array(
            'title' => 'Ice Cream',
            'content' => 'Choice of vanilla, chocolate, or strawberry',
            'price' => 'Included',
            'category' => 'kids'
        )
    );

    $created = 0;
    foreach ($menu_items as $item) {
        $existing = get_page_by_title($item['title'], OBJECT, 'menu_item');
        if (!$existing) {
            $post_id = wp_insert_post(array(
                'post_title' => $item['title'],
                'post_content' => $item['content'],
                'post_status' => 'publish',
                'post_type' => 'menu_item'
            ));

            if ($post_id) {
                update_post_meta($post_id, '_menu_item_price', $item['price']);
                
                $term = get_term_by('slug', $item['category'], 'menu_category');
                if ($term) {
                    wp_set_object_terms($post_id, $term->term_id, 'menu_category');
                }
                $created++;
            }
        }
    }

    return $created;
}

// Tema aktifleştirildiğinde demo verileri oluştur
function restaurant_theme_activate_demo_data() {
    if (!get_option('restaurant_theme_demo_data_created')) {
        restaurant_create_demo_menu_items();
        
        // Demo testimonial oluştur
        $testimonials = array(
            array(
                'title' => 'Incredible meal!',
                'content' => 'Incredible meal at the new Cappadocia Restaurant! Exceptional food, superb service, and a lovely atmosphere. It\'s clear a lot of care and passion goes into everything they do. Very impressed and will definitely be back soon. Highly recommend!',
                'author' => 'Nihad B'
            ),
            array(
                'title' => 'Very welcoming',
                'content' => 'Very welcoming, lovely polite staff. Food is more than delicious, lots of variety of food and drinks. Will definitely be returning.',
                'author' => 'Emma R'
            ),
            array(
                'title' => 'Everything is very delicious',
                'content' => 'Everything is very delicious, the food service is fast, the employees are very attentive.',
                'author' => 'Cemile C'
            )
        );
        
        foreach ($testimonials as $testimonial) {
            $existing = get_page_by_title($testimonial['title'], OBJECT, 'testimonial');
            if (!$existing) {
                $post_id = wp_insert_post(array(
                    'post_title' => $testimonial['title'],
                    'post_content' => $testimonial['content'],
                    'post_status' => 'publish',
                    'post_type' => 'testimonial'
                ));
                
                if ($post_id) {
                    update_post_meta($post_id, '_testimonial_author', $testimonial['author']);
                }
            }
        }
        
        update_option('restaurant_theme_demo_data_created', true);
    }
}
add_action('after_switch_theme', 'restaurant_theme_activate_demo_data');

// Admin bar'da demo veri oluşturma butonu
function restaurant_admin_bar_demo_data($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'restaurant-demo-data',
        'title' => '<span class="ab-icon dashicons dashicons-food"></span>' . __('Create Demo Menu Items', 'restaurant-theme'),
        'href' => '#',
        'meta' => array(
            'onclick' => 'restaurantCreateDemoData(); return false;',
            'title' => __('Create demo menu items with categories', 'restaurant-theme')
        )
    ));
}
add_action('admin_bar_menu', 'restaurant_admin_bar_demo_data', 100);

function restaurant_admin_demo_data_script() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <script>
    function restaurantCreateDemoData() {
        if (confirm('<?php _e('This will create demo menu items with categories. Continue?', 'restaurant-theme'); ?>')) {
            jQuery.post(ajaxurl, {
                action: 'restaurant_create_demo_data',
                nonce: '<?php echo wp_create_nonce('restaurant_demo_data'); ?>'
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        }
    }
    </script>
    <?php
}
add_action('admin_footer', 'restaurant_admin_demo_data_script');

// AJAX handler for demo data
function restaurant_create_demo_data_ajax() {
    check_ajax_referer('restaurant_demo_data', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied', 'restaurant-theme')));
    }
    
    $created = restaurant_create_demo_menu_items();
    
    wp_send_json_success(array(
        'message' => sprintf(__('%d demo menu items created successfully!', 'restaurant-theme'), $created)
    ));
}
add_action('wp_ajax_restaurant_create_demo_data', 'restaurant_create_demo_data_ajax');