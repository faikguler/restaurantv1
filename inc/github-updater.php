<?php
/**
 * GitHub Auto Updater
 * Tema klasör yapısını otomatik düzeltir
 * 
 * @package Restaurant_Theme
 */

if (!defined('ABSPATH')) exit;

class Restaurant_Theme_GitHub_Updater {
    
    private $github_username = 'faikguler';
    private $github_repository = 'restaurantv1';
    private $theme_slug = 'restaurantv1';
    
    public function __construct() {
        add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_update'));
        add_filter('upgrader_source_selection', array($this, 'fix_github_folder'), 10, 4);
    }
    
    /**
     * GitHub'dan güncelleme kontrolü
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote = $this->get_remote_version();
        if (!$remote) {
            return $transient;
        }
        
        $current = wp_get_theme($this->theme_slug)->get('Version');
        $current = str_replace('v', '', $current);
        $remote_ver = str_replace('v', '', $remote['version']);
        
        if (version_compare($current, $remote_ver, '<')) {
            $transient->response[$this->theme_slug] = array(
                'theme'        => $this->theme_slug,
                'new_version'  => $remote_ver,
                'url'          => $remote['url'],
                'package'      => $remote['package'],
            );
        }
        
        return $transient;
    }
    
    /**
     * GitHub API'den versiyon bilgisi
     */
    private function get_remote_version() {
        $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repository}/releases/latest";
        
        $response = wp_remote_get($api_url, array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response));
        
        if (!isset($data->tag_name)) {
            return false;
        }
        
        return array(
            'version' => $data->tag_name,
            'package' => $data->zipball_url,
            'url'     => $data->html_url,
        );
    }
    
    /**
     * GitHub klasör yapısını düzelt
     * Gelen: wp-content/upgrade/faikguler-restaurantv1-7ce4f29/
     * Olması gereken: wp-content/upgrade/restaurantv1/
     */
    public function fix_github_folder($source, $remote_source, $upgrader, $hook_extra = null) {
        global $wp_filesystem;
        
        // Sadece tema güncellemelerinde çalış
        if (!isset($hook_extra['theme']) || $hook_extra['theme'] !== $this->theme_slug) {
            return $source;
        }
        
        // Hedef klasör adı
        $new_source = trailingslashit($remote_source) . $this->theme_slug . '/';
        
        // Kaynak klasörde style.css var mı kontrol et
        if (!$wp_filesystem->exists($source . 'style.css')) {
            // İlk alt klasörü bul
            $files = $wp_filesystem->dirlist($source);
            
            if (is_array($files)) {
                foreach ($files as $file => $info) {
                    if ($info['type'] == 'd') {
                        $subfolder = trailingslashit($source) . $file . '/';
                        
                        // Alt klasörde style.css var mı?
                        if ($wp_filesystem->exists($subfolder . 'style.css')) {
                            $source = $subfolder;
                            break;
                        }
                    }
                }
            }
        }
        
        // style.css hala yoksa hata ver
        if (!$wp_filesystem->exists($source . 'style.css')) {
            return new WP_Error('no_theme_files', 'Tema dosyaları bulunamadı.');
        }
        
        // Klasörü doğru isme taşı
        if ($source !== $new_source) {
            if ($wp_filesystem->move($source, $new_source)) {
                return $new_source;
            } else {
                return new WP_Error('move_failed', 'Tema klasörü taşınamadı.');
            }
        }
        
        return $new_source;
    }
}

// Sınıfı başlat
new Restaurant_Theme_GitHub_Updater();