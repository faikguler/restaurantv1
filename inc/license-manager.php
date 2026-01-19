<?php
/**
 * License Manager (Future Premium Feature)
 */

if (!defined('ABSPATH')) exit;

class Restaurant_Theme_License_Manager {
    
    private $license_key;
    private $license_status;
    private $api_url = 'https://api.faikguler.com'; // Kendi API'nizi oluşturun
    
    public function __construct() {
        $this->license_key = get_option('restaurant_theme_license_key', '');
        $this->license_status = get_option('restaurant_theme_license_status', 'inactive');
        
        // Admin menüsü ekle
        add_action('admin_menu', array($this, 'add_license_menu'));
        
        // AJAX işleyicileri
        add_action('wp_ajax_activate_license', array($this, 'ajax_activate_license'));
        add_action('wp_ajax_deactivate_license', array($this, 'ajax_deactivate_license'));
    }
    
    public function add_license_menu() {
        add_submenu_page(
            'themes.php',
            __('Tema Lisansı', 'restaurant-theme'),
            __('Tema Lisansı', 'restaurant-theme'),
            'manage_options',
            'restaurant-theme-license',
            array($this, 'license_page')
        );
    }
    
    public function license_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Restaurant Theme Lisans Yönetimi', 'restaurant-theme'); ?></h1>
            
            <div class="license-form">
                <h2><?php _e('Lisansınızı Aktifleştirin', 'restaurant-theme'); ?></h2>
                
                <form id="licenseActivationForm">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="license_key"><?php _e('Lisans Anahtarı', 'restaurant-theme'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="license_key" name="license_key" 
                                       value="<?php echo esc_attr($this->license_key); ?>" 
                                       class="regular-text" 
                                       placeholder="<?php esc_attr_e('Lisans anahtarınızı girin', 'restaurant-theme'); ?>">
                                <p class="description">
                                    <?php _e('Lisans anahtarınızı almak için: <a href="https://www.faikguler.com" target="_blank">faikguler.com</a>', 'restaurant-theme'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <?php wp_nonce_field('restaurant_theme_license_nonce', 'license_nonce'); ?>
                        <button type="button" id="activateLicense" class="button button-primary">
                            <?php _e('Lisansı Aktifleştir', 'restaurant-theme'); ?>
                        </button>
                        <button type="button" id="deactivateLicense" class="button button-secondary">
                            <?php _e('Lisansı Devre Dışı Bırak', 'restaurant-theme'); ?>
                        </button>
                    </p>
                </form>
                
                <div id="licenseMessage" style="display:none; margin-top: 20px;"></div>
                
                <div class="license-status" style="margin-top: 30px; padding: 20px; background: #f5f5f5;">
                    <h3><?php _e('Lisans Durumu', 'restaurant-theme'); ?></h3>
                    <p>
                        <strong><?php _e('Durum:', 'restaurant-theme'); ?></strong>
                        <span id="currentLicenseStatus">
                            <?php echo $this->license_status == 'active' ? 
                                '<span style="color: green;">✓ ' . __('Aktif', 'restaurant-theme') . '</span>' : 
                                '<span style="color: red;">✗ ' . __('Aktif Değil', 'restaurant-theme') . '</span>'; ?>
                        </span>
                    </p>
                    <?php if (!empty($this->license_key)): ?>
                    <p>
                        <strong><?php _e('Lisans Anahtarı:', 'restaurant-theme'); ?></strong>
                        <code><?php echo esc_html(substr($this->license_key, 0, 10) . '...'); ?></code>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#activateLicense').on('click', function() {
                const licenseKey = $('#license_key').val();
                const nonce = $('#license_nonce').val();
                
                if (!licenseKey) {
                    alert('<?php _e('Lütfen lisans anahtarını girin.', 'restaurant-theme'); ?>');
                    return;
                }
                
                $(this).prop('disabled', true).text('<?php _e('Aktifleştiriliyor...', 'restaurant-theme'); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'activate_license',
                        license_key: licenseKey,
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#licenseMessage').html(
                                '<div class="notice notice-success"><p>' + 
                                response.data.message + 
                                '</p></div>'
                            ).show();
                            $('#currentLicenseStatus').html(
                                '<span style="color: green;">✓ <?php _e('Aktif', 'restaurant-theme'); ?></span>'
                            );
                        } else {
                            $('#licenseMessage').html(
                                '<div class="notice notice-error"><p>' + 
                                response.data.message + 
                                '</p></div>'
                            ).show();
                        }
                    },
                    error: function() {
                        $('#licenseMessage').html(
                            '<div class="notice notice-error"><p><?php _e('Bir hata oluştu.', 'restaurant-theme'); ?></p></div>'
                        ).show();
                    },
                    complete: function() {
                        $('#activateLicense').prop('disabled', false).text('<?php _e('Lisansı Aktifleştir', 'restaurant-theme'); ?>');
                    }
                });
            });
            
            $('#deactivateLicense').on('click', function() {
                if (!confirm('<?php _e('Lisansı devre dışı bırakmak istediğinize emin misiniz?', 'restaurant-theme'); ?>')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('<?php _e('Devre Dışı Bırakılıyor...', 'restaurant-theme'); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'deactivate_license',
                        license_key: '<?php echo esc_js($this->license_key); ?>',
                        nonce: $('#license_nonce').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#licenseMessage').html(
                                '<div class="notice notice-success"><p>' + 
                                response.data.message + 
                                '</p></div>'
                            ).show();
                            $('#currentLicenseStatus').html(
                                '<span style="color: red;">✗ <?php _e('Aktif Değil', 'restaurant-theme'); ?></span>'
                            );
                            $('#license_key').val('');
                        } else {
                            $('#licenseMessage').html(
                                '<div class="notice notice-error"><p>' + 
                                response.data.message + 
                                '</p></div>'
                            ).show();
                        }
                    },
                    complete: function() {
                        $('#deactivateLicense').prop('disabled', false).text('<?php _e('Lisansı Devre Dışı Bırak', 'restaurant-theme'); ?>');
                    }
                });
            });
        });
        </script>
        
        <style>
        .license-form {
            max-width: 600px;
            margin: 20px 0;
        }
        .license-status {
            border-radius: 5px;
            border-left: 4px solid #0073aa;
        }
        </style>
        <?php
    }
    
    public function ajax_activate_license() {
        check_ajax_referer('restaurant_theme_license_nonce', 'nonce');
        
        $license_key = sanitize_text_field($_POST['license_key']);
        
        // API'ye istek gönder (örnek - gerçek implementasyon için kendi API'nizi oluşturun)
        $api_params = array(
            'action' => 'activate_license',
            'license' => $license_key,
            'domain' => home_url(),
            'theme' => 'restaurant-theme'
        );
        
        $response = wp_remote_post($this->api_url, array(
            'body' => $api_params,
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => __('API bağlantı hatası.', 'restaurant-theme')
            ));
        }
        
        $license_data = json_decode(wp_remote_retrieve_body($response));
        
        if ($license_data->success) {
            update_option('restaurant_theme_license_key', $license_key);
            update_option('restaurant_theme_license_status', 'active');
            update_option('restaurant_theme_license_data', $license_data);
            
            wp_send_json_success(array(
                'message' => __('Lisans başarıyla aktifleştirildi!', 'restaurant-theme')
            ));
        } else {
            wp_send_json_error(array(
                'message' => $license_data->message ?: __('Geçersiz lisans anahtarı.', 'restaurant-theme')
            ));
        }
    }
    
    public function ajax_deactivate_license() {
        check_ajax_referer('restaurant_theme_license_nonce', 'nonce');
        
        $license_key = get_option('restaurant_theme_license_key', '');
        
        // API'ye istek gönder
        $api_params = array(
            'action' => 'deactivate_license',
            'license' => $license_key,
            'domain' => home_url()
        );
        
        $response = wp_remote_post($this->api_url, array(
            'body' => $api_params,
            'timeout' => 15
        ));
        
        delete_option('restaurant_theme_license_key');
        delete_option('restaurant_theme_license_status');
        delete_option('restaurant_theme_license_data');
        
        wp_send_json_success(array(
            'message' => __('Lisans başarıyla devre dışı bırakıldı.', 'restaurant-theme')
        ));
    }
    
    /**
     * Premium özellikleri kontrol et
     */
    public function has_premium_feature($feature) {
        $license_data = get_option('restaurant_theme_license_data', array());
        
        if (empty($license_data) || $this->license_status !== 'active') {
            return false;
        }
        
        // Özellik kontrolü burada yapılacak
        // Örnek: return in_array($feature, $license_data->features);
        
        return true;
    }
}

// Gelecekte premium için lisans yöneticisini başlat
// new Restaurant_Theme_License_Manager();