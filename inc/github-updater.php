<?php
/**
 * GitHub Auto Updater
 */

if (!defined('ABSPATH')) exit;

class Restaurant_Theme_GitHub_Updater {
    
    private $slug;
    private $theme_data;
    private $github_username;
    private $github_repository;
    private $github_response;
    private $github_token;
    
    public function __construct() {
        // Tema bilgilerini al
        $this->slug = basename(get_template_directory());
        
        // GitHub bilgileriniz
        $this->github_username = 'faikguler'; // GitHub kullanıcı adınız
        $this->github_repository = 'restaurantv1'; // GitHub repository adı
        $this->github_token = ''; // Private repo için token (gerekirse)
        
        // Eylemleri başlat
        add_filter('pre_set_site_transient_update_themes', array($this, 'modify_transient'), 10, 1);
        add_filter('upgrader_pre_install', array($this, 'pre_install'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);
        add_action('wp_ajax_check_theme_updates', array($this, 'ajax_check_updates'));
        
        // Admin bildirimi
        add_action('admin_notices', array($this, 'show_update_notice'));
        
        // Güncelleme kontrolü zamanlayıcı
        if (!wp_next_scheduled('restaurant_theme_daily_update_check')) {
            wp_schedule_event(time(), 'daily', 'restaurant_theme_daily_update_check');
        }
        add_action('restaurant_theme_daily_update_check', array($this, 'check_for_updates'));
    }
    
    /**
     * GitHub'dan güncelleme bilgilerini al
     */
    private function get_repository_info() {
        if (is_null($this->github_response)) {
            $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', 
                $this->github_username, 
                $this->github_repository
            );
            
            // GitHub API isteği
            $args = array(
                'headers' => array(
                    'User-Agent' => 'WordPress-Restaurant-Theme',
                ),
            );
            
            // Eğer private repo ve token varsa
            if (!empty($this->github_token)) {
                $args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($request_uri, $args);
            
            if (is_wp_error($response)) {
                return false;
            }
            
            $this->github_response = @json_decode(wp_remote_retrieve_body($response));
        }
        
        return $this->github_response;
    }
    
    /**
     * Transient'i güncelle
     */
    public function modify_transient($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $github_data = $this->get_repository_info();
        
        if (!$github_data || !isset($github_data->tag_name)) {
            return $transient;
        }
        
        // Mevcut tema versiyonu
        $current_version = wp_get_theme($this->slug)->get('Version');
        
        // GitHub'daki versiyon
        $github_version = ltrim($github_data->tag_name, 'v');
        
        // Versiyon karşılaştırması
        if (version_compare($current_version, $github_version, '<')) {
            $theme_data = array(
                'theme'       => $this->slug,
                'new_version' => $github_version,
                'url'         => 'https://github.com/' . $this->github_username . '/' . $this->github_repository,
                'package'     => $github_data->zipball_url,
            );
            
            // Eğer private repo ve token varsa, package URL'sine token ekle
            if (!empty($this->github_token)) {
                $theme_data['package'] = add_query_arg(
                    array('access_token' => $this->github_token),
                    $theme_data['package']
                );
            }
            
            $transient->response[$this->slug] = $theme_data;
        }
        
        return $transient;
    }
    
    /**
     * Güncelleme öncesi işlemler
     */
    public function pre_install($response, $hook_extra, $result) {
        // Gerekirse tema yedeği al
        if (!defined('RESTAURANT_THEME_BACKUP')) {
            define('RESTAURANT_THEME_BACKUP', true);
            // Yedekleme işlemi buraya eklenebilir
        }
        
        return $response;
    }
    
    /**
     * Güncelleme sonrası işlemler
     */
    public function post_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        
        $install_directory = get_template_directory();
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        
        if ($this->slug == get_stylesheet()) {
            switch_theme($this->slug);
        }
        
        // Güncelleme sonrası cache temizleme
        wp_cache_flush();
        
        return $result;
    }
    
    /**
     * Güncelleme kontrolü
     */
    public function check_for_updates() {
        delete_site_transient('update_themes');
        wp_update_themes();
    }
    
    /**
     * Admin bildirimi göster
     */
    public function show_update_notice() {
        $github_data = $this->get_repository_info();
        
        if (!$github_data || !isset($github_data->tag_name)) {
            return;
        }
        
        $current_version = wp_get_theme($this->slug)->get('Version');
        $github_version = ltrim($github_data->tag_name, 'v');
        
        if (version_compare($current_version, $github_version, '<')) {
            ?>
            <div class="notice notice-warning is-dismissible notice-restaurant-theme-update">
                <p>
                    <strong><?php _e('Restaurant Theme Güncelleme', 'restaurant-theme'); ?></strong> -
                    <?php printf(__('Yeni versiyon (%s) mevcut. Mevcut versiyonunuz: %s', 'restaurant-theme'), 
                        $github_version, 
                        $current_version
                    ); ?>
                    <a href="<?php echo admin_url('update-core.php'); ?>">
                        <?php _e('Güncellemek için tıklayın', 'restaurant-theme'); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * AJAX güncelleme kontrolü
     */
    public function ajax_check_updates() {
        check_ajax_referer('restaurant_theme_nonce', 'nonce');
        
        $this->check_for_updates();
        
        wp_send_json_success(array(
            'message' => __('Güncellemeler kontrol edildi.', 'restaurant-theme')
        ));
    }
}

// Güncelleme sistemini başlat
new Restaurant_Theme_GitHub_Updater();