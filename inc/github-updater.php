<?php
/**
 * GitHub Auto Updater - GÜVENLİ VERSİYON
 */

if (!defined('ABSPATH')) exit;

class Restaurant_Theme_GitHub_Updater {
    
    private $slug;
    private $theme_data;
    private $github_username;
    private $github_repository;
    private $github_response;
    private $github_token;
    private $backup_dir;
    
    public function __construct() {
        // Tema bilgilerini al
        $this->slug = basename(get_template_directory());
        
        // GitHub bilgileriniz
        $this->github_username = 'faikguler';
        $this->github_repository = 'restaurantv1';
        $this->github_token = '';
        
        // Yedek klasörü
        $this->backup_dir = WP_CONTENT_DIR . '/restaurant-theme-backups/';
        
        // SADECE bu filtreyi kullanın, diğerlerini KALDIRIN
        add_filter('pre_set_site_transient_update_themes', array($this, 'modify_transient'), 10, 1);
        add_action('admin_notices', array($this, 'show_update_notice'));
        
        // AJAX güncelleme kontrolü
        add_action('wp_ajax_check_theme_updates', array($this, 'ajax_check_updates'));
        
        // Güncelleme kontrolü zamanlayıcı
        if (!wp_next_scheduled('restaurant_theme_daily_update_check')) {
            wp_schedule_event(time(), 'daily', 'restaurant_theme_daily_update_check');
        }
        add_action('restaurant_theme_daily_update_check', array($this, 'check_for_updates'));
        
        // Manuel güncelleme butonu için
        add_action('admin_bar_menu', array($this, 'add_admin_bar_update_button'), 100);
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
            
            $args = array(
                'headers' => array(
                    'User-Agent' => 'WordPress-Restaurant-Theme',
                ),
                'timeout' => 30,
            );
            
            if (!empty($this->github_token)) {
                $args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($request_uri, $args);
            
            if (is_wp_error($response)) {
                error_log('GitHub Updater Error: ' . $response->get_error_message());
                return false;
            }
            
            $this->github_response = json_decode(wp_remote_retrieve_body($response));
        }
        
        return $this->github_response;
    }
    
    /**
     * Transient'i güncelle - SADECE BURASI ÇALIŞSIN
     */
    public function modify_transient($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $github_data = $this->get_repository_info();
        
        if (!$github_data || !isset($github_data->tag_name)) {
            return $transient;
        }
        
        // Versiyon karşılaştırması
        $current_version = wp_get_theme($this->slug)->get('Version');
        $current_version_clean = str_replace('v', '', $current_version);
        $github_version = str_replace('v', '', $github_data->tag_name);
        
        if (version_compare($current_version_clean, $github_version, '<')) {
            $theme_data = array(
                'theme'       => $this->slug,
                'new_version' => $github_version,
                'url'         => 'https://github.com/' . $this->github_username . '/' . $this->github_repository,
                'package'     => $github_data->zipball_url,
                'requires'    => '5.0',
                'requires_php' => '7.2'
            );
            
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
     * Yedek oluştur
     */
    private function create_backup() {
        if (!wp_mkdir_p($this->backup_dir)) {
            error_log('GitHub Updater: Yedek klasörü oluşturulamadı: ' . $this->backup_dir);
            return false;
        }
        
        $theme_dir = get_template_directory();
        $backup_file = $this->backup_dir . 'restaurant-theme-backup-' . date('Y-m-d-H-i-s') . '.zip';
        
        // Basit yedekleme - sadece önemli dosyalar
        $files_to_backup = array(
            'style.css',
            'functions.php',
            'header.php',
            'footer.php',
            'index.php',
            'page.php'
        );
        
        // Yedekleme işlemi buraya eklenebilir
        // Gerçek bir yedekleme için ZipArchive kullanılabilir
        
        return true;
    }
    
    /**
     * Admin bar'a güncelleme butonu ekle
     */
    public function add_admin_bar_update_button($wp_admin_bar) {
        if (!current_user_can('update_themes')) {
            return;
        }
        
        $github_data = $this->get_repository_info();
        if (!$github_data || !isset($github_data->tag_name)) {
            return;
        }
        
        $current_version = wp_get_theme($this->slug)->get('Version');
        $current_version_clean = str_replace('v', '', $current_version);
        $github_version = str_replace('v', '', $github_data->tag_name);
        
        if (version_compare($current_version_clean, $github_version, '<')) {
            $wp_admin_bar->add_node(array(
                'id' => 'github-updater-manual',
                'title' => '<span class="ab-icon dashicons dashicons-update"></span> ' . 
                          sprintf(__('Tema Güncelle: v%s', 'restaurant-theme'), $github_version),
                'href' => admin_url('update-core.php'),
                'meta' => array(
                    'class' => 'github-updater-notice'
                )
            ));
        }
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
        if (!current_user_can('update_themes')) {
            return;
        }
        
        $github_data = $this->get_repository_info();
        
        if (!$github_data || !isset($github_data->tag_name)) {
            return;
        }
        
        $current_version = str_replace('v', '', wp_get_theme($this->slug)->get('Version'));
        $github_version = str_replace('v', '', $github_data->tag_name);
        
        if (version_compare($current_version, $github_version, '<')) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php _e('🔄 Restaurant Theme Güncelleme Mevcut', 'restaurant-theme'); ?></strong><br>
                    <?php printf(__('Yeni versiyon <strong>v%s</strong> mevcut. Mevcut versiyonunuz: v%s', 'restaurant-theme'), 
                        $github_version, 
                        $current_version
                    ); ?>
                    <br>
                    <a href="<?php echo admin_url('update-core.php'); ?>" class="button button-primary" style="margin-top: 10px;">
                        <?php _e('Güncellemeyi Başlat', 'restaurant-theme'); ?>
                    </a>
                    <a href="https://github.com/<?php echo $this->github_username; ?>/<?php echo $this->github_repository; ?>/releases" 
                       target="_blank" class="button" style="margin-top: 10px;">
                        <?php _e('Değişiklikleri Gör', 'restaurant-theme'); ?>
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