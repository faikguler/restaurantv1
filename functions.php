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


