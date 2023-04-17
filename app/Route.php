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

namespace WDR_OD\App;

use WDR_OD\App\Controllers\Admin\Admin;
use WDR_OD\App\Helpers\Helper;

defined('ABSPATH') or exit;

class Route
{

    private static $admin,$helper;

    /**
     * Init the hooks
     * @return void
     */
    public function hooks() {
        self::$admin = empty( self::$admin) ? new Admin() : self::$admin;
        self::$helper = empty( self::$helper) ? new Helper() : self::$helper;

        $settings_data = get_option('wdr_omnibus_directive');
        $is_override_omnibus_message = $settings_data['is_override_omnibus_message'];
        $is_omnibus_plugin_active = self::$helper->isOmnibusPluginActive();
        $is_override_omnibus_message = isset($is_override_omnibus_message) ? $is_override_omnibus_message : 0;
        $is_omnibus_plugin_active = isset($is_omnibus_plugin_active) ? $is_omnibus_plugin_active : 0;

        if($is_override_omnibus_message == 1 && $is_omnibus_plugin_active == 1){
            add_filter('iworks_omnibus_message_template', array(self::$admin, 'mergeOmnibusMessageWithDiscountRule'), 10, 3);
        } else {
            $show_omnibus_message = $settings_data['show_omnibus_message_option'];
            if(isset($show_omnibus_message) && !empty($show_omnibus_message)){
                $position_to_show_message = $settings_data['position_to_show_message'];
                $position_to_show_message = is_string($position_to_show_message) ? $position_to_show_message : "woocommerce_single_product_summary";
                $position_to_show_message = apply_filters('advanced_woo_discount_rules_omnibus_directive_show_message_position', $position_to_show_message);
                add_filter($position_to_show_message, array(self::$admin, 'separateOmnibusMessageForDiscountRule'));
            }
        }

        add_action('woocommerce_product_options_pricing', array(self::$admin, 'showLowestPriceInProductEditPage'), 1 );
        add_filter('plugin_action_links_' . WDR_OD_PLUGIN_BASENAME, array(self::$admin, 'wdrOmActionLink'));
        add_filter('advanced_woo_discount_rules_page_addons', array(self::$helper, 'omnibusAddon'));
        add_action('admin_init',array(self::$admin, 'saveSettingsData'));
        add_action('admin_enqueue_scripts', array(self::$admin,'scriptFiles'));

        if (isset($_GET['saved'])) {
            $message = $_GET['saved'];
            switch ($message) {
                case $message == "true":
                    add_action('admin_notices', array(self::$admin,'successNotice'));
                    break;
                case $message == "false":
                    add_action('admin_notices', array(self::$admin,'errorNotice'));
            }
        }
    }
}