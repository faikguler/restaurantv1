<?php
/**
 * Meta Boxes
 */

if (!defined('ABSPATH')) exit;

// Add Meta Boxes for Menu Items
function restaurant_menu_meta_boxes() {
    add_meta_box(
        'menu_item_details',
        'Menu Item Details',
        'restaurant_menu_details_callback',
        'menu_item',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'restaurant_menu_meta_boxes');

function restaurant_menu_details_callback($post) {
    wp_nonce_field('menu_item_details_nonce', 'menu_item_nonce');
    
    $price = get_post_meta($post->ID, '_menu_item_price', true);
    $price_alt = get_post_meta($post->ID, '_menu_item_price_alt', true);
    $dietary = get_post_meta($post->ID, '_menu_item_dietary', true);
    
    ?>
    <p>
        <label for="menu_item_price"><strong>Price:</strong></label><br>
        <input type="text" id="menu_item_price" name="menu_item_price" value="<?php echo esc_attr($price); ?>" style="width: 100%;" placeholder="£15.95">
    </p>
    <p>
        <label for="menu_item_price_alt"><strong>Alternative Price (optional):</strong></label><br>
        <input type="text" id="menu_item_price_alt" name="menu_item_price_alt" value="<?php echo esc_attr($price_alt); ?>" style="width: 100%;" placeholder="£26.95">
    </p>
    <p>
        <label for="menu_item_dietary"><strong>Dietary Information (optional):</strong></label><br>
        <input type="text" id="menu_item_dietary" name="menu_item_dietary" value="<?php echo esc_attr($dietary); ?>" style="width: 100%;" placeholder="Vegetarian, Vegan, Gluten-Free">
    </p>
    <?php
}

function restaurant_save_menu_details($post_id) {
    if (!isset($_POST['menu_item_nonce']) || !wp_verify_nonce($_POST['menu_item_nonce'], 'menu_item_details_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['menu_item_price'])) {
        update_post_meta($post_id, '_menu_item_price', sanitize_text_field($_POST['menu_item_price']));
    }
    
    if (isset($_POST['menu_item_price_alt'])) {
        update_post_meta($post_id, '_menu_item_price_alt', sanitize_text_field($_POST['menu_item_price_alt']));
    }
    
    if (isset($_POST['menu_item_dietary'])) {
        update_post_meta($post_id, '_menu_item_dietary', sanitize_text_field($_POST['menu_item_dietary']));
    }
}
add_action('save_post_menu_item', 'restaurant_save_menu_details');

// Add Meta Boxes for Testimonials
function restaurant_testimonial_meta_boxes() {
    add_meta_box(
        'testimonial_author',
        'Testimonial Author',
        'restaurant_testimonial_author_callback',
        'testimonial',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'restaurant_testimonial_meta_boxes');

function restaurant_testimonial_author_callback($post) {
    wp_nonce_field('testimonial_author_nonce', 'testimonial_nonce');
    
    $author = get_post_meta($post->ID, '_testimonial_author', true);
    
    ?>
    <p>
        <label for="testimonial_author"><strong>Author Name:</strong></label><br>
        <input type="text" id="testimonial_author" name="testimonial_author" value="<?php echo esc_attr($author); ?>" style="width: 100%;" placeholder="John D.">
    </p>
    <?php
}

function restaurant_save_testimonial_author($post_id) {
    if (!isset($_POST['testimonial_nonce']) || !wp_verify_nonce($_POST['testimonial_nonce'], 'testimonial_author_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['testimonial_author'])) {
        update_post_meta($post_id, '_testimonial_author', sanitize_text_field($_POST['testimonial_author']));
    }
}
add_action('save_post_testimonial', 'restaurant_save_testimonial_author');

// Add Meta Boxes for Reservations
function restaurant_reservation_meta_boxes() {
    add_meta_box(
        'reservation_details',
        __('Reservation Details', 'restaurant-theme'),
        'restaurant_reservation_details_callback',
        'reservation',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'restaurant_reservation_meta_boxes');

function restaurant_reservation_details_callback($post) {
    wp_nonce_field('reservation_details_nonce', 'reservation_nonce');
    
    $name = get_post_meta($post->ID, 'reservation_name', true);
    $email = get_post_meta($post->ID, 'reservation_email', true);
    $phone = get_post_meta($post->ID, 'reservation_phone', true);
    $date = get_post_meta($post->ID, 'reservation_date', true);
    $time = get_post_meta($post->ID, 'reservation_time', true);
    $guests = get_post_meta($post->ID, 'reservation_guests', true);
    $status = get_post_meta($post->ID, 'reservation_status', true);
    $message = get_post_meta($post->ID, 'reservation_message', true);
    ?>
    
    <style>
        .reservation-meta-box {
            padding: 15px;
        }
        .reservation-field {
            margin-bottom: 15px;
        }
        .reservation-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .reservation-field input,
        .reservation-field select,
        .reservation-field textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .status-pending { color: #f39c12; }
        .status-confirmed { color: #27ae60; }
        .status-cancelled { color: #e74c3c; }
        .status-completed { color: #3498db; }
    </style>
    
    <div class="reservation-meta-box">
        <div class="reservation-field">
            <label for="reservation_name"><?php _e('Name', 'restaurant-theme'); ?></label>
            <input type="text" id="reservation_name" name="reservation_name" value="<?php echo esc_attr($name); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_email"><?php _e('Email', 'restaurant-theme'); ?></label>
            <input type="email" id="reservation_email" name="reservation_email" value="<?php echo esc_attr($email); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_phone"><?php _e('Phone', 'restaurant-theme'); ?></label>
            <input type="text" id="reservation_phone" name="reservation_phone" value="<?php echo esc_attr($phone); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_date"><?php _e('Date', 'restaurant-theme'); ?></label>
            <input type="date" id="reservation_date" name="reservation_date" value="<?php echo esc_attr($date); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_time"><?php _e('Time', 'restaurant-theme'); ?></label>
            <input type="time" id="reservation_time" name="reservation_time" value="<?php echo esc_attr($time); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_guests"><?php _e('Number of Guests', 'restaurant-theme'); ?></label>
            <input type="number" id="reservation_guests" name="reservation_guests" value="<?php echo esc_attr($guests); ?>">
        </div>
        
        <div class="reservation-field">
            <label for="reservation_status"><?php _e('Status', 'restaurant-theme'); ?></label>
            <select id="reservation_status" name="reservation_status">
                <option value="pending" <?php selected($status, 'pending'); ?> class="status-pending"><?php _e('Pending', 'restaurant-theme'); ?></option>
                <option value="confirmed" <?php selected($status, 'confirmed'); ?> class="status-confirmed"><?php _e('Confirmed', 'restaurant-theme'); ?></option>
                <option value="cancelled" <?php selected($status, 'cancelled'); ?> class="status-cancelled"><?php _e('Cancelled', 'restaurant-theme'); ?></option>
                <option value="completed" <?php selected($status, 'completed'); ?> class="status-completed"><?php _e('Completed', 'restaurant-theme'); ?></option>
            </select>
        </div>
        
        <div class="reservation-field">
            <label for="reservation_message"><?php _e('Special Requests', 'restaurant-theme'); ?></label>
            <textarea id="reservation_message" name="reservation_message" rows="4"><?php echo esc_textarea($message); ?></textarea>
        </div>
        
        <div class="reservation-field">
            <label><?php _e('Submission Details', 'restaurant-theme'); ?></label>
            <p>
                <?php 
                $ip = get_post_meta($post->ID, 'reservation_ip', true);
                $date_created = get_the_date('Y-m-d H:i:s', $post->ID);
                echo sprintf(__('IP Address: %s', 'restaurant-theme'), $ip ? $ip : 'N/A') . '<br>';
                echo sprintf(__('Submitted on: %s', 'restaurant-theme'), $date_created);
                ?>
            </p>
        </div>
    </div>
    
    <?php
}

function restaurant_save_reservation_details($post_id) {
    if (!isset($_POST['reservation_nonce']) || !wp_verify_nonce($_POST['reservation_nonce'], 'reservation_details_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array(
        'reservation_name',
        'reservation_email',
        'reservation_phone',
        'reservation_date',
        'reservation_time',
        'reservation_guests',
        'reservation_status',
        'reservation_message'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Update post title to reflect changes
    $name = get_post_meta($post_id, 'reservation_name', true);
    $date = get_post_meta($post_id, 'reservation_date', true);
    $time = get_post_meta($post_id, 'reservation_time', true);
    $status = get_post_meta($post_id, 'reservation_status', true);
    
    if ($name && $date && $time) {
        $new_title = 'Reservation - ' . $name . ' - ' . $date . ' ' . $time . ' (' . ucfirst($status) . ')';
        
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $new_title
        ));
    }
}
add_action('save_post_reservation', 'restaurant_save_reservation_details');