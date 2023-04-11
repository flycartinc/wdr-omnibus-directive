<?php
/**
 * Woo Discount Rules: Omnibus Directive
 *
 * @package   wdr-omnibus-directive
 * @author    Kirubanithi G <kirubanithi@flycart.org>
 * @copyright 2022 Flycart
 * @license   GPL-3.0-or-later
 * @link      https://flycart.org
 */

namespace WDR_OD\App\Controllers\Admin;

defined('ABSPATH') or exit;

class Compatibility
{

    /**
     * Check Php environment compatible
     * @return bool|int
     */
    function isAWDRODEnvironmentCompatible() {
        return version_compare(PHP_VERSION, WDR_OD_PHP_REQUIRED_VERSION, '>=');
    }

    /**
     * Check WooCommerce active or not
     * @return bool
     */
    function isAWDRODWooActive() {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('woocommerce/woocommerce.php', $active_plugins, false) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }

    /**
     * Check Discount Rules for WooCommerce active or not
     * @return bool
     */
    function isAWDRCorePluginActive() {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('woo-discount-rules/woo-discount-rules.php', $active_plugins, false) || array_key_exists('woo-discount-rules/woo-discount-rules.php', $active_plugins);
    }

    /**
     * Check WooCommerce version
     * @return mixed|null
     */
    function getAWDRODWooVersion() {
        if (defined('WC_VERSION')) {
            return WC_VERSION;
        }
        if (!function_exists('get_plugins')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_folder = get_plugins('/woocommerce');
        $plugin_file = 'woocommerce.php';
        $wc_installed_version = NULL;
        if (isset($plugin_folder[$plugin_file]['Version'])) {
            $wc_installed_version = $plugin_folder[$plugin_file]['Version'];
        }
        return $wc_installed_version;
    }

    /**
     * Check WooCommerce version compatible
     * @return bool|int
     */
    function isAWDRODWooCompatible() {
        $current_wc_version = $this->getAWDRODWooVersion();
        return version_compare($current_wc_version, WDR_OD_WC_REQUIRED_VERSION, '>=');
    }

    /**
     * Check WordPress version compatible
     * @return bool|int
     */
    function isAWDRODWpCompatible() {
        return version_compare(get_bloginfo('version'), WDR_OD_WP_REQUIRED_VERSION, '>=');
    }

    /**
     * Check the version compatible and required plugin active status
     * @return void
     */
    function checkVersion() {
        if (!$this->isAWDRODEnvironmentCompatible()) {
            exit(__('Woo Discount Rules: Omnibus Directive can not be activated because it requires minimum PHP version of ', 'wdr-omnibus-directive') . ' ' . esc_attr_e(WDR_OD_PHP_REQUIRED_VERSION));
        }
        if (!$this->isAWDRODWooActive()) {
            exit(__('Woocommerce must installed and activated in-order to use Woo Discount Rules: Omnibus Directive!', 'wdr-omnibus-directive'));
        }
        if (!$this->isAWDRODWooCompatible()) {
            exit(__(' Woo Discount Rules: Omnibus Directive requires at least Woocommerce', 'wdr-omnibus-directive') . ' ' . esc_attr_e(WDR_OD_WC_REQUIRED_VERSION));
        }
        if (!$this->isAWDRODWpCompatible()) {
            exit(__(' Woo Discount Rules: Omnibus Directive requires at least WordPress', 'wdr-omnibus-directive') . ' ' . esc_attr_e(WDR_OD_WP_REQUIRED_VERSION));
        }
        if (!$this->isAWDRCorePluginActive()) {
            exit(__('Discount Rules for WooCommerce must installed and activated in-order to use Woo Discount Rules: Omnibus Directive!', 'wdr-omnibus-directive'));
        }
    }
}
