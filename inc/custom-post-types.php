<?php
/**
 * Custom Post Types
 */

if (!defined('ABSPATH')) exit;

// Custom Post Type: Menu Items
function restaurant_menu_post_type() {
    $labels = array(
        'name' => 'Menu Items',
        'singular_name' => 'Menu Item',
        'add_new' => 'Add New Menu Item',
        'add_new_item' => 'Add New Menu Item',
        'edit_item' => 'Edit Menu Item',
        'new_item' => 'New Menu Item',
        'view_item' => 'View Menu Item',
        'search_items' => 'Search Menu Items',
        'not_found' => 'No menu items found',
        'not_found_in_trash' => 'No menu items found in trash'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-food',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'show_in_menu' => true,
        'menu_position' => 20
    );
    
    register_post_type('menu_item', $args);
}
add_action('init', 'restaurant_menu_post_type');

// Menu Categories Taxonomy
function restaurant_menu_taxonomy() {
    $labels = array(
        'name' => 'Menu Categories',
        'singular_name' => 'Menu Category',
        'search_items' => 'Search Categories',
        'all_items' => 'All Categories',
        'edit_item' => 'Edit Category',
        'update_item' => 'Update Category',
        'add_new_item' => 'Add New Category',
        'new_item_name' => 'New Category Name',
        'menu_name' => 'Categories'
    );
    
    register_taxonomy('menu_category', 'menu_item', array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'menu-category')
    ));
}
add_action('init', 'restaurant_menu_taxonomy');

// Custom Post Type: Testimonials
function restaurant_testimonials_post_type() {
    $labels = array(
        'name' => 'Testimonials',
        'singular_name' => 'Testimonial',
        'add_new' => 'Add New Testimonial',
        'add_new_item' => 'Add New Testimonial',
        'edit_item' => 'Edit Testimonial',
        'new_item' => 'New Testimonial',
        'view_item' => 'View Testimonial',
        'search_items' => 'Search Testimonials'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-testimonial',
        'supports' => array('title', 'editor'),
        'show_in_rest' => true,
        'show_in_menu' => true,
        'menu_position' => 21
    );
    
    register_post_type('testimonial', $args);
}
add_action('init', 'restaurant_testimonials_post_type');

// Custom Post Type: Reservations
function restaurant_reservation_post_type() {
    $labels = array(
        'name' => __('Reservations', 'restaurant-theme'),
        'singular_name' => __('Reservation', 'restaurant-theme'),
        'add_new' => __('Add New', 'restaurant-theme'),
        'add_new_item' => __('Add New Reservation', 'restaurant-theme'),
        'edit_item' => __('Edit Reservation', 'restaurant-theme'),
        'new_item' => __('New Reservation', 'restaurant-theme'),
        'view_item' => __('View Reservation', 'restaurant-theme'),
        'search_items' => __('Search Reservations', 'restaurant-theme'),
        'not_found' => __('No reservations found', 'restaurant-theme'),
        'not_found_in_trash' => __('No reservations found in trash', 'restaurant-theme'),
        'menu_name' => __('Reservations', 'restaurant-theme')
    );
    
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => false,
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 22,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'custom-fields'),
        'capabilities' => array(
            'create_posts' => false
        ),
        'map_meta_cap' => true
    );
    
    register_post_type('reservation', $args);
}
add_action('init', 'restaurant_reservation_post_type');

// Custom columns for Reservations
function restaurant_reservation_columns($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Reservation', 'restaurant-theme'),
        'name' => __('Name', 'restaurant-theme'),
        'email' => __('Email', 'restaurant-theme'),
        'date' => __('Date', 'restaurant-theme'),
        'time' => __('Time', 'restaurant-theme'),
        'guests' => __('Guests', 'restaurant-theme'),
        'status' => __('Status', 'restaurant-theme'),
        'date_created' => __('Submitted', 'restaurant-theme')
    );
    return $columns;
}
add_filter('manage_reservation_posts_columns', 'restaurant_reservation_columns');

function restaurant_reservation_custom_column($column, $post_id) {
    switch ($column) {
        case 'name':
            echo esc_html(get_post_meta($post_id, 'reservation_name', true));
            break;
        case 'email':
            echo esc_html(get_post_meta($post_id, 'reservation_email', true));
            break;
        case 'date':
            echo esc_html(get_post_meta($post_id, 'reservation_date', true));
            break;
        case 'time':
            echo esc_html(get_post_meta($post_id, 'reservation_time', true));
            break;
        case 'guests':
            echo esc_html(get_post_meta($post_id, 'reservation_guests', true));
            break;
        case 'status':
            $status = get_post_meta($post_id, 'reservation_status', true);
            $status_labels = array(
                'pending' => '<span class="status-pending" style="color: #f39c12; font-weight: bold;">' . __('Pending', 'restaurant-theme') . '</span>',
                'confirmed' => '<span class="status-confirmed" style="color: #27ae60; font-weight: bold;">' . __('Confirmed', 'restaurant-theme') . '</span>',
                'cancelled' => '<span class="status-cancelled" style="color: #e74c3c; font-weight: bold;">' . __('Cancelled', 'restaurant-theme') . '</span>',
                'completed' => '<span class="status-completed" style="color: #3498db; font-weight: bold;">' . __('Completed', 'restaurant-theme') . '</span>'
            );
            echo isset($status_labels[$status]) ? $status_labels[$status] : $status;
            break;
        case 'date_created':
            echo get_the_date('Y-m-d H:i', $post_id);
            break;
    }
}
add_action('manage_reservation_posts_custom_column', 'restaurant_reservation_custom_column', 10, 2);

// Make reservation columns sortable
function restaurant_reservation_sortable_columns($columns) {
    $columns['date'] = 'reservation_date';
    $columns['status'] = 'reservation_status';
    $columns['date_created'] = 'date';
    return $columns;
}
add_filter('manage_edit-reservation_sortable_columns', 'restaurant_reservation_sortable_columns');

// Handle sorting for custom columns
function restaurant_reservation_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ('reservation_date' == $orderby) {
        $query->set('meta_key', 'reservation_date');
        $query->set('orderby', 'meta_value');
    }
    
    if ('reservation_status' == $orderby) {
        $query->set('meta_key', 'reservation_status');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'restaurant_reservation_orderby');