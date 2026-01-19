<?php
/**
 * Admin Functions
 */

if (!defined('ABSPATH')) exit;

// Add custom admin CSS for reservation status
function restaurant_admin_css() {
    echo '<style>
        .status-pending { color: #f39c12 !important; }
        .status-confirmed { color: #27ae60 !important; }
        .status-cancelled { color: #e74c3c !important; }
        .status-completed { color: #3498db !important; }
        .column-status { width: 100px; }
        .column-date_created { width: 150px; }
        .column-name { width: 150px; }
        .column-email { width: 200px; }
    </style>';
}
add_action('admin_head', 'restaurant_admin_css');

// Add dashboard widget for recent reservations
function restaurant_dashboard_widget() {
    wp_add_dashboard_widget(
        'restaurant_recent_reservations',
        __('Recent Reservations', 'restaurant-theme'),
        'restaurant_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'restaurant_dashboard_widget');

function restaurant_dashboard_widget_content() {
    $args = array(
        'post_type' => 'reservation',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $reservations = new WP_Query($args);
    
    if ($reservations->have_posts()) {
        echo '<ul style="margin: 0; padding: 0;">';
        
        while ($reservations->have_posts()) {
            $reservations->the_post();
            $name = get_post_meta(get_the_ID(), 'reservation_name', true);
            $date = get_post_meta(get_the_ID(), 'reservation_date', true);
            $time = get_post_meta(get_the_ID(), 'reservation_time', true);
            $status = get_post_meta(get_the_ID(), 'reservation_status', true);
            
            echo '<li style="margin-bottom: 10px; padding: 10px; background: #f5f5f5; border-radius: 3px;">';
            echo '<strong>' . esc_html($name) . '</strong><br>';
            echo esc_html($date) . ' at ' . esc_html($time) . '<br>';
            
            $status_color = '';
            switch ($status) {
                case 'pending': $status_color = '#f39c12'; break;
                case 'confirmed': $status_color = '#27ae60'; break;
                case 'cancelled': $status_color = '#e74c3c'; break;
                case 'completed': $status_color = '#3498db'; break;
            }
            
            echo '<span style="color: ' . $status_color . '; font-weight: bold;">' . ucfirst($status) . '</span>';
            echo '<br><a href="' . get_edit_post_link() . '">' . __('View Details', 'restaurant-theme') . '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        
        echo '<p><a href="' . admin_url('edit.php?post_type=reservation') . '">' . __('View All Reservations', 'restaurant-theme') . '</a></p>';
    } else {
        echo '<p>' . __('No reservations yet.', 'restaurant-theme') . '</p>';
    }
    
    wp_reset_postdata();
}

// Add reservation count to admin menu
function restaurant_admin_menu_count() {
    global $menu;
    
    $count = wp_count_posts('reservation');
    $pending_count = $count->pending;
    
    if ($pending_count > 0) {
        foreach ($menu as $key => $value) {
            if ($menu[$key][2] == 'edit.php?post_type=reservation') {
                $menu[$key][0] .= ' <span class="update-plugins count-' . $pending_count . '"><span class="plugin-count">' . $pending_count . '</span></span>';
                break;
            }
        }
    }
}
add_action('admin_menu', 'restaurant_admin_menu_count');

// Email template for reservation notifications
function restaurant_reservation_email_template($email_content, $reservation_id) {
    $name = get_post_meta($reservation_id, 'reservation_name', true);
    $email = get_post_meta($reservation_id, 'reservation_email', true);
    $phone = get_post_meta($reservation_id, 'reservation_phone', true);
    $date = get_post_meta($reservation_id, 'reservation_date', true);
    $time = get_post_meta($reservation_id, 'reservation_time', true);
    $guests = get_post_meta($reservation_id, 'reservation_guests', true);
    $message = get_post_meta($reservation_id, 'reservation_message', true);
    
    $template = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reservation Confirmation</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #C41E3A; margin: 0;">' . get_bloginfo('name') . '</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Reservation Confirmation</p>
            </div>
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h2 style="color: #C41E3A; margin-top: 0;">Reservation Details</h2>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Name:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Email:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($email) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Phone:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($phone) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Date:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($date) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Time:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($time) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Guests:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($guests) . '</td>
                    </tr>';
    
    if (!empty($message)) {
        $template .= '<tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; vertical-align: top;"><strong>Special Requests:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;">' . esc_html($message) . '</td>
                    </tr>';
    }
    
    $template .= '</table>
            </div>
            
            <div style="background: #fff8e1; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 20px;">
                <p style="margin: 0;"><strong>Important:</strong> This is a reservation request. Your table will be confirmed once you receive a confirmation call from us.</p>
            </div>
            
            <div style="text-align: center; color: #666; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee;">
                <p>' . get_bloginfo('name') . ' &copy; ' . date('Y') . '</p>
                <p>' . get_theme_mod('address', '') . '</p>
                <p>Phone: ' . get_theme_mod('phone', '') . ' | Email: ' . get_theme_mod('email', '') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}

// Developer credit in admin footer
function restaurant_admin_footer_text($text) {
    return '<span id="footer-thankyou">' . sprintf(__('Thank you for using Restaurant Theme v1. Developed by <a href="%s" target="_blank">Faik Guler</a>.', 'restaurant-theme'), 'https://www.faikguler.com') . '</span>';
}
add_filter('admin_footer_text', 'restaurant_admin_footer_text');

// Sayfaları manuel oluşturmak için (geçici)
function restaurant_manual_create_pages() {
    if (isset($_GET['create_pages']) && $_GET['create_pages'] == '1') {
        if (current_user_can('manage_options')) {
            restaurant_theme_create_default_pages();
            echo '<div class="notice notice-success"><p>' . __('Pages created successfully!', 'restaurant-theme') . '</p></div>';
        }
    }
}
add_action('admin_notices', 'restaurant_manual_create_pages');

// Geçici reset kodu
function reset_page_creation_option() {
    if (isset($_GET['reset_pages']) && $_GET['reset_pages'] == '1') {
        if (current_user_can('manage_options')) {
            delete_option('restaurant_theme_default_pages_created');
            echo '<div class="notice notice-success"><p>' . __('Page creation flag reset successfully!', 'restaurant-theme') . '</p></div>';
        }
    }
}
add_action('admin_notices', 'reset_page_creation_option');

// Demo data reset
function reset_demo_data_option() {
    if (isset($_GET['reset_demo_data']) && $_GET['reset_demo_data'] == '1') {
        if (current_user_can('manage_options')) {
            delete_option('restaurant_theme_demo_data_created');
            echo '<div class="notice notice-success"><p>' . __('Demo data flag reset successfully!', 'restaurant-theme') . '</p></div>';
        }
    }
}
add_action('admin_notices', 'reset_demo_data_option');