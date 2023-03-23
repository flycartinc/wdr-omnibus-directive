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
    public function hooks()
    {
        self::$admin = empty( self::$admin) ? new Admin() : self::$admin;
        self::$helper = empty( self::$helper) ? new Helper() : self::$helper;

        $is_override_omnibus_message = get_option('_is_override_omnibus_message');
        $is_override_omnibus_message = isset($is_override_omnibus_message) ? $is_override_omnibus_message : 0;
        if($is_override_omnibus_message == 1){
            add_filter('iworks_omnibus_message_template', array(self::$admin, 'mergeOmnibusMessageWithDiscountRule'), 10, 3);
        } else {
            $show_omnibus_message = get_option('_awdr_show_omnibus_message');
            if(isset($show_omnibus_message) && !empty($show_omnibus_message)){
                $position_to_show_message = is_string(get_option('_awdr_position_to_show_message')) ? get_option('_awdr_position_to_show_message') : "woocommerce_single_product_summary";
                add_filter($position_to_show_message, array(self::$admin, 'separateOmnibusMessageForDiscountRule'));
            }
        }

        add_action('woocommerce_product_options_pricing', array(self::$admin, 'showLowestPriceInProductEditPage'), 1 );

        add_filter('plugin_action_links_' . WDR_OD_PLUGIN_BASENAME, array( self::$admin, 'wdrOmActionLink' ));
        add_filter('advanced_woo_discount_rules_page_addons', array(self::$helper, 'omnibusAddon'));
        add_action('admin_init',array(self::$admin, 'saveSettingsData'));
        add_action('admin_enqueue_scripts', array(self::$admin,'scriptFiles'));
    }
}