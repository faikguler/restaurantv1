<?php
/**
 * Restaurant Theme v1 Functions
 * 
 * @package Restaurant_Theme
 * @author Faik Guler
 * @link https://www.faikguler.com
 */

if (!defined('ABSPATH')) exit;

// Define theme constants
$theme_data = wp_get_theme();
define('RESTAURANT_THEME_VERSION', $theme_data->get('Version'));
define('RESTAURANT_THEME_DIR', get_template_directory());
define('RESTAURANT_THEME_URI', get_template_directory_uri());

// Include all modular files
require_once RESTAURANT_THEME_DIR . '/inc/theme-setup.php';
require_once RESTAURANT_THEME_DIR . '/inc/custom-post-types.php';
require_once RESTAURANT_THEME_DIR . '/inc/customizer-settings.php';
require_once RESTAURANT_THEME_DIR . '/inc/ajax-handlers.php';
require_once RESTAURANT_THEME_DIR . '/inc/meta-boxes.php';
require_once RESTAURANT_THEME_DIR . '/inc/shortcodes.php';
require_once RESTAURANT_THEME_DIR . '/inc/admin-functions.php';
require_once RESTAURANT_THEME_DIR . '/inc/demo-data.php';

// Include GitHub updater (comment out if not needed immediately)
require_once RESTAURANT_THEME_DIR . '/inc/github-updater.php';

// Include license manager (for future premium features)
// require_once RESTAURANT_THEME_DIR . '/inc/license-manager.php';

// Initialize theme
add_action('after_setup_theme', 'restaurant_theme_init');

function restaurant_theme_init() {
    // Theme initialization hook
    do_action('restaurant_theme_loaded');
}




add_action('admin_notices', function() {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['action']) && $_GET['action'] == 'do-theme-upgrade') {
        echo '<div class="notice notice-warning">';
        echo '<p><strong>Debug: Güncelleme Başladı</strong></p>';
        
        // Güncelleme transient'ini kontrol et
        $transient = get_site_transient('update_themes');
        if ($transient && isset($transient->response['restaurantv1'])) {
            echo '<p>Tema güncellemesi bulundu.</p>';
        }
        
        // Bakım modu kontrolü
        if (file_exists(ABSPATH . '.maintenance')) {
            echo '<p>Bakım modu dosyası oluşturuldu.</p>';
        }
        
        echo '</div>';
    }

    $current_version = RESTAURANT_THEME_VERSION;
    echo '<div class="notice notice-info">';
    echo '<p><strong>GitHub Updater Debug:</strong></p>';
    echo '<p>Current Version: <code>' . $current_version . '</code></p>';
    
    // API'yi kontrol et
    $response = wp_remote_get('https://api.github.com/repos/faikguler/restaurantv1/releases/latest', array(
        'headers' => array('User-Agent' => 'WordPress-Restaurant-Theme')
    ));
    
    if (is_wp_error($response)) {
        echo '<p>Error: ' . $response->get_error_message() . '</p>';
    } else {
        $data = json_decode(wp_remote_retrieve_body($response));
        
        if (isset($data->tag_name)) {
            echo '<p>GitHub Latest Release: <code>' . $data->tag_name . '</code></p>';
            echo '<p>Zipball URL: <code>' . $data->zipball_url . '</code></p>';
            
            $github_version = ltrim($data->tag_name, 'v');
            $current_clean = str_replace('v', '', $current_version);
            $needs_update = version_compare($current_clean, $github_version, '<');
            
            echo '<p>Needs Update: <code>' . ($needs_update ? 'YES' : 'NO') . '</code></p>';
            
        } else {
            echo '<p>No release data found</p>';
        }
    }
    
    echo '</div>';
});


// Güncelleme öncesi yedekleme ve güvenlik
add_action('upgrader_process_complete', 'restaurant_theme_backup_before_update', 10, 2);

function restaurant_theme_backup_before_update($upgrader_object, $options) {
    if ($options['action'] == 'update' && $options['type'] == 'theme') {
        $current_theme = wp_get_theme();
        $current_theme_slug = $current_theme->get_stylesheet();
        
        // Sadece Restaurant Theme güncellenirken
        if (in_array($current_theme_slug, $options['themes'])) {
            // Customizer ayarlarını yedekle
            $customizer_backup = get_theme_mods();
            update_option('restaurant_theme_customizer_backup_' . date('Y-m-d-H-i-s'), $customizer_backup);
            
            // Tema seçeneklerini yedekle
            $theme_options = get_option('theme_mods_' . $current_theme_slug);
            update_option('restaurant_theme_options_backup_' . date('Y-m-d-H-i-s'), $theme_options);
            
            error_log('Restaurant Theme: Güncelleme öncesi yedekleme tamamlandı.');
        }
    }
}

// Güncelleme sonrası temayı tekrar aktif et
add_action('upgrader_process_complete', 'restaurant_theme_reactivate_after_update', 20, 2);

function restaurant_theme_reactivate_after_update($upgrader_object, $options) {
    if ($options['action'] == 'update' && $options['type'] == 'theme') {
        $theme_slug = 'restaurantv1'; // Tema klasör adınız
        
        // Tema güncellendikten sonra tekrar aktif et
        if (in_array($theme_slug, $options['themes'])) {
            // 2 saniye bekle
            sleep(2);
            
            // Temayı aktif et
            switch_theme($theme_slug);
            
            // Cache'i temizle
            wp_cache_flush();
            
            error_log('Restaurant Theme: Güncelleme sonrası tema yeniden aktif edildi.');
        }
    }
}

// Admin bar'da hızlı yedekleme butonu
add_action('admin_bar_menu', 'restaurant_theme_admin_bar_backup_button', 101);

function restaurant_theme_admin_bar_backup_button($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'theme-backup-now',
        'title' => '<span class="ab-icon dashicons dashicons-backup"></span> ' . __('Tema Yedeği Al', 'restaurant-theme'),
        'href' => '#',
        'meta' => array(
            'onclick' => 'restaurantThemeBackupNow(); return false;',
            'title' => __('Customizer ayarlarını yedekle', 'restaurant-theme')
        )
    ));
}

// AJAX yedekleme
add_action('wp_ajax_restaurant_theme_backup_now', 'restaurant_theme_ajax_backup');

function restaurant_theme_ajax_backup() {
    check_ajax_referer('restaurant_theme_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Yetkiniz yok.', 'restaurant-theme')));
    }
    
    $current_theme = wp_get_theme();
    $theme_slug = $current_theme->get_stylesheet();
    
    // Customizer yedekle
    $customizer_data = get_theme_mods();
    $backup_id = 'manual_backup_' . date('Y-m-d-H-i-s');
    
    update_option('restaurant_theme_customizer_' . $backup_id, array(
        'date' => current_time('mysql'),
        'theme' => $theme_slug,
        'version' => $current_theme->get('Version'),
        'data' => $customizer_data
    ));
    
    wp_send_json_success(array(
        'message' => __('Yedekleme tamamlandı!', 'restaurant-theme'),
        'backup_id' => $backup_id
    ));
}

// JavaScript for admin
add_action('admin_footer', 'restaurant_theme_backup_script');

function restaurant_theme_backup_script() {
    ?>
    <script>
    function restaurantThemeBackupNow() {
        if (confirm('Customizer ayarlarınızı yedeklemek istediğinize emin misiniz?')) {
            jQuery.post(ajaxurl, {
                action: 'restaurant_theme_backup_now',
                nonce: restaurantTheme.nonce
            }, function(response) {
                if (response.success) {
                    alert('✓ ' + response.data.message);
                } else {
                    alert('✗ ' + response.data.message);
                }
            });
        }
    }
    </script>
    <?php
}


