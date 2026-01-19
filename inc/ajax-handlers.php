<?php
/**
 * AJAX Handlers
 */

if (!defined('ABSPATH')) exit;

// Reservation AJAX Handler
function restaurant_handle_reservation() {
    // Security check
    if (!check_ajax_referer('restaurant_theme_nonce', 'nonce', false)) {
        wp_send_json_error(array(
            'message' => __('Security check failed.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    // Sanitize data
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $date = sanitize_text_field($_POST['date']);
    $time = sanitize_text_field($_POST['time']);
    $guests = sanitize_text_field($_POST['guests']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || empty($guests)) {
        wp_send_json_error(array(
            'message' => __('Please fill all required fields.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    // Email to admin
    $to = get_theme_mod('email', get_option('admin_email'));
    if (empty($to)) {
        $to = get_option('admin_email');
    }
    
    $subject = 'New Reservation Request - ' . get_bloginfo('name');
    
    $body = "<html><body style='font-family: Arial, sans-serif;'>";
    $body .= "<h2 style='color: #C41E3A;'>New Reservation Request</h2>";
    $body .= "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Restaurant:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>" . get_bloginfo('name') . "</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Name:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$name</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Email:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$email</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Phone:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$phone</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Date:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$date</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Time:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$time</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Number of Guests:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$guests</td></tr>";
    
    if (!empty($message)) {
        $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Special Requests:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$message</td></tr>";
    }
    
    $body .= "</table>";
    $body .= "<p style='margin-top: 20px; color: #666; font-size: 12px;'>This reservation request was submitted from " . get_bloginfo('url') . " on " . current_time('Y-m-d H:i:s') . "</p>";
    $body .= "</body></html>";
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    // Send email to admin
    $admin_sent = wp_mail($to, $subject, $body, $headers);
    
    // Send confirmation email to customer
    $customer_subject = 'Reservation Request Received - ' . get_bloginfo('name');
    $customer_body = "<html><body style='font-family: Arial, sans-serif;'>";
    $customer_body .= "<h2 style='color: #C41E3A;'>Thank You for Your Reservation Request</h2>";
    $customer_body .= "<p>Dear $name,</p>";
    $customer_body .= "<p>We have received your reservation request for <strong>$date at $time</strong> for <strong>$guests guests</strong>.</p>";
    $customer_body .= "<p>We will review your request and contact you shortly to confirm your booking.</p>";
    
    if (!empty($message)) {
        $customer_body .= "<p><strong>Your Special Request:</strong><br>$message</p>";
    }
    
    $customer_body .= "<p><strong>Our Contact Information:</strong></p>";
    $customer_body .= "<ul>";
    
    $address = get_theme_mod('address');
    $phone_setting = get_theme_mod('phone');
    $email_setting = get_theme_mod('email');
    
    if ($address) {
        $customer_body .= "<li><strong>Address:</strong> " . nl2br($address) . "</li>";
    }
    if ($phone_setting) {
        $customer_body .= "<li><strong>Phone:</strong> " . $phone_setting . "</li>";
    }
    if ($email_setting) {
        $customer_body .= "<li><strong>Email:</strong> " . $email_setting . "</li>";
    }
    
    $customer_body .= "</ul>";
    $customer_body .= "<p>Best regards,<br><strong>" . get_bloginfo('name') . " Team</strong></p>";
    $customer_body .= "</body></html>";
    
    $customer_sent = wp_mail($email, $customer_subject, $customer_body, $headers);
    
    // Save to database as custom post type
    $reservation_data = array(
        'post_title'    => 'Reservation - ' . $name . ' - ' . $date . ' ' . $time,
        'post_content'  => $message,
        'post_status'   => 'publish',
        'post_type'     => 'reservation',
        'meta_input'    => array(
            'reservation_name'    => $name,
            'reservation_email'   => $email,
            'reservation_phone'   => $phone,
            'reservation_date'    => $date,
            'reservation_time'    => $time,
            'reservation_guests'  => $guests,
            'reservation_status'  => 'pending',
            'reservation_ip'      => $_SERVER['REMOTE_ADDR']
        )
    );
    
    $reservation_id = wp_insert_post($reservation_data);
    
    if ($admin_sent && $reservation_id) {
        wp_send_json_success(array(
            'message' => __('Thank you! Your reservation has been submitted. We will contact you shortly to confirm your booking.', 'restaurant-theme')
        ));
    } elseif ($reservation_id) {
        // If email failed but reservation was saved
        wp_send_json_success(array(
            'message' => __('Your reservation has been saved. You will receive a confirmation call from us soon.', 'restaurant-theme')
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Sorry, there was an error submitting your reservation. Please try again or call us directly.', 'restaurant-theme')
        ));
    }
    
    wp_die();
}
add_action('wp_ajax_restaurant_reservation', 'restaurant_handle_reservation');
add_action('wp_ajax_nopriv_restaurant_reservation', 'restaurant_handle_reservation');

// Contact Form AJAX Handler
function restaurant_handle_contact() {
    // Security check
    if (!check_ajax_referer('restaurant_theme_nonce', 'nonce', false)) {
        wp_send_json_error(array(
            'message' => __('Security check failed.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    // Sanitize data
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $method = sanitize_text_field($_POST['method']);
    $message = sanitize_textarea_field($_POST['message']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(array(
            'message' => __('Please fill all required fields.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'restaurant-theme')
        ));
        wp_die();
    }
    
    $to = get_theme_mod('email', get_option('admin_email'));
    if (empty($to)) {
        $to = get_option('admin_email');
    }
    
    $subject = 'New Contact Message - ' . get_bloginfo('name');
    
    $body = "<html><body style='font-family: Arial, sans-serif;'>";
    $body .= "<h2 style='color: #C41E3A;'>New Contact Message</h2>";
    $body .= "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>From:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$name</td></tr>";
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Email:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$email</td></tr>";
    
    if (!empty($phone)) {
        $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Phone:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$phone</td></tr>";
    }
    
    if (!empty($method)) {
        $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'><strong>Preferred Contact Method:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$method</td></tr>";
    }
    
    $body .= "<tr><td style='padding: 10px; border: 1px solid #ddd; background: #f9f9f9; vertical-align: top;'><strong>Message:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>$message</td></tr>";
    $body .= "</table>";
    $body .= "<p style='margin-top: 20px; color: #666; font-size: 12px;'>This message was submitted from " . get_bloginfo('url') . " on " . current_time('Y-m-d H:i:s') . "</p>";
    $body .= "</body></html>";
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    $sent = wp_mail($to, $subject, $body, $headers);
    
    if ($sent) {
        wp_send_json_success(array(
            'message' => __('Thank you for your message! We will get back to you as soon as possible.', 'restaurant-theme')
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Sorry, there was an error sending your message. Please try again or call us directly.', 'restaurant-theme')
        ));
    }
    
    wp_die();
}
add_action('wp_ajax_restaurant_contact', 'restaurant_handle_contact');
add_action('wp_ajax_nopriv_restaurant_contact', 'restaurant_handle_contact');

// AJAX handler for creating pages
function restaurant_create_pages_ajax() {
    check_ajax_referer('restaurant_create_pages', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('You do not have permission to perform this action.', 'restaurant-theme'));
        wp_die();
    }
    
    // Force recreate pages
    $pages = restaurant_theme_create_default_pages(true);
    
    if (!empty($pages)) {
        $message = sprintf(
            __('%d pages created successfully! Please go to <a href="%s" target="_blank">Appearance > Menus</a> to set up your navigation menu.', 'restaurant-theme'),
            count($pages),
            admin_url('nav-menus.php')
        );
        wp_send_json_success(array('message' => $message));
    } else {
        $message = __('All pages already exist or were created successfully. Please check your pages list and set up your menu.', 'restaurant-theme');
        wp_send_json_success(array('message' => $message));
    }
    
    wp_die();
}
add_action('wp_ajax_restaurant_create_pages', 'restaurant_create_pages_ajax');