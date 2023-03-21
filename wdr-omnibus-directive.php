<?php
/**
 * Woo Discount Rules: Omnibus Directive
 *
 * @package           wdr-omnibus-directive
 * @author            Kirubanithi G <kirubanithi@flycart.org
>
 * @copyright         2022 Flycart
 * @license           GPL-3.0-or-later
 * @link              https://flycart.org
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Discount Rules: Omnibus Directive
 * Plugin URI:        https://flycart.org
 * Description:       It helps to display the lowest price of a product for the last n days.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Flycart
 * Author URI:        https://flycart.org
 * Text Domain:       wdr-omnibus-directive
 * Domain Path:       /i18n/languages
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') or exit;

// define basic plugin constants
defined('WDR_OD_PLUGIN_FILE') or define('WDR_OD_PLUGIN_FILE', __FILE__);
defined('WDR_OD_PLUGIN_PATH') or define('WDR_OD_PLUGIN_PATH', plugin_dir_path(__FILE__));
defined('WDR_OD_PLUGIN_URL') or define('WDR_OD_PLUGIN_URL', plugin_dir_url(__FILE__));

// To load composer autoload (psr-4)
if (file_exists(WDR_OD_PLUGIN_PATH . '/vendor/autoload.php')) {
    require WDR_OD_PLUGIN_PATH . '/vendor/autoload.php';
} else {
    wp_die('Woo Discount Rules: Omnibus Directive is unable to find the autoload file.');
}

// Call the Route class
if (class_exists('WDR_OD\App\Route')) {
    $route =  new WDR_OD\App\Route();
    $route->hooks();
} else {
    wp_die(__('Woo Discount Rules: Omnibus Directive is unable to find the Route class.'));
}
