<?php
/**
 * Plugin Name:       Woo Discount Rules: Omnibus Directive
 * Plugin URI:        https://github.com/flycartinc/wdr-omnibus-directive-public
 * Description:       Plugin displays the lowest discount price of product applied through Discount Rules.
 * Version:           1.0.0 Beta-8
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
    define('WDR_OD_VERSION', '1.0.0 Beta-8');
}

/**
 * The plugin file
 */
if (!defined('WDR_OD_PLUGIN_FILE')) {
    define('WDR_OD_PLUGIN_FILE', __FILE__);
}

/**
 * The plugin path
 */
if (!defined('WDR_OD_PLUGIN_PATH')) {
    define('WDR_OD_PLUGIN_PATH', plugin_dir_path(WDR_OD_PLUGIN_FILE));
}

/**
 * The plugin url
 */
if (!defined('WDR_OD_PLUGIN_URL')) {
    define('WDR_OD_PLUGIN_URL', plugin_dir_url(WDR_OD_PLUGIN_FILE));
}

/**
 * The plugin base name
 */
if (!defined('WDR_OD_PLUGIN_BASENAME')) {
    define('WDR_OD_PLUGIN_BASENAME', plugin_basename(WDR_OD_PLUGIN_FILE));
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
register_activation_hook(WDR_OD_PLUGIN_FILE, 'wdrOdPluginActivate');
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
                load_plugin_textdomain( 'wdr-omnibus-directive', false, basename( dirname( WDR_OD_PLUGIN_FILE ) ) . '/i18n/languages/' );
            }
            do_action('wdr_omnibus_directive_loaded');
        }
    }
}, 1);

/**
 * Git configuration for update
 */
require WDR_OD_PLUGIN_PATH.'/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/flycartinc/wdr-omnibus-directive-public',
    WDR_OD_PLUGIN_FILE,
    'wdr-omnibus-directive'
);
$myUpdateChecker->setBranch('main');