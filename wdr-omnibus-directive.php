<?php
/**
 * Plugin Name:       Woo Discount Rules: Omnibus Directive
 * Plugin URI:        https://www.flycart.org/products/wordpress/woocommerce-discount-rules/addons/
 * Description:       Plugin displays the lowest discount price of product applied through Discount Rules.
 * Version:           1.0.0 Beta-1
 * Author:            Flycart
 * Author URI:        https://flycart.org
 * Text Domain:       wdr-omnibus-directive
 * Slug:              wdr-omnibus-directive
 * Domain Path:       /i18n/languages
 * Requires at least: 5.2
 * WC requires at least: 5.0
 * WC tested up to: 7.3
 */

defined('ABSPATH') or exit;

/**
 * Current version of our app
 */
if (!defined('WDR_OD_VERSION')) {
    define('WDR_OD_VERSION', '1.0.0 Beta-1');
}

/**
 * The plugin path
 */
if (!defined('WDR_OD_PLUGIN_PATH')) {
    define('WDR_OD_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

/**
 * The plugin url
 */
if (!defined('WDR_OD_PLUGIN_URL')) {
    define('WDR_OD_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/**
 * The plugin base name
 */
if (!defined('WDR_OD_PLUGIN_BASENAME')) {
    define('WDR_OD_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * Required Php Version
 */
if (!defined('WDR_OD_PHP_REQUIRED_VERSION')) {
    define('WDR_OD_PHP_REQUIRED_VERSION', 5.6);
}

/**
 * Required WooCommerce Version
 */
if (!defined('WDR_OD_WC_REQUIRED_VERSION')) {
    define('WDR_OD_WC_REQUIRED_VERSION', '5.0');
}

/**
 * Required WordPress Version
 */
if (!defined('WDR_OD_WP_REQUIRED_VERSION')) {
    define('WDR_OD_WP_REQUIRED_VERSION', '5.2');
}

/**
 * Required Discount Rule Version
 */
if (!defined('WDR_OD_WDR_REQUIRED_VERSION')) {
    define('WDR_OD_WDR_REQUIRED_VERSION', '2.6.0');
}

/**
 * Package - autoload
 */
if (!file_exists(WDR_OD_PLUGIN_PATH . '/vendor/autoload.php')) {
    return false;
} else {
    require WDR_OD_PLUGIN_PATH . '/vendor/autoload.php';
}

/**
 * Check compatibility
 */
register_activation_hook(__FILE__, 'wdrOdPluginActivate');
function wdrOdPluginActivate() {
    if (class_exists('WDR_OD\App\Controllers\Admin\Compatibility')) {
        $Compatibility = new WDR_OD\App\Controllers\Admin\Compatibility();
        $Compatibility->checkVersion();
    } else {
        wp_die(__('Woo Discount Rules: Omnibus Directive is unable to find the Compatibility class.'));
    }
}

/**
 * Call the Route class
 */
add_action('plugins_loaded', function() {
    if (class_exists('WooCommerce') && class_exists('Wdr\App\Router')) {
        if (class_exists('WDR_OD\App\Route')) {
            do_action('wdr_omnibus_directive_before_loaded');
            $route =  new WDR_OD\App\Route();
            $route->hooks();
            if(function_exists('load_plugin_textdomain')){
                load_plugin_textdomain( 'wdr-omnibus-directive', false, basename( dirname( __FILE__ ) ) . '/i18n/languages/' );
            }
            do_action('wdr_omnibus_directive_loaded');
        }
    }
}, 1);
