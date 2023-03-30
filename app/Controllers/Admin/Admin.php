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
use WDR_OD\App\Helpers\Helper;

defined('ABSPATH') or exit;

class Admin
{
    /**
     * @var Helper
     */
    private static $helper;

    public function __construct()
    {
        self::$helper = empty( self::$helper) ? new Helper() : self::$helper;
    }

    /**
     * Show minimum price message using omnibus event
     * @param $message
     * @param $price
     * @param $price_lowest
     * @return mixed|string
     */
    public static function mergeOmnibusMessageWithDiscountRule($message, $price, $price_lowest) {

        $is_eligible = self::$helper->checkRuleId();
        if(isset($is_eligible) && empty($is_eligible)){
            return $message;
        }

        $min_price = self::$helper->omnibusForDiscountRules();
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $custom_message = get_option('_awdr_om_message');
            $message = isset($custom_message) && !empty($custom_message)? $custom_message : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('advanced_woo_discount_rules_omnibus_directive_message_date_format_for_omnibus',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
        }
        return apply_filters('advanced_woo_discount_rules_omnibus_directive_message_for_omnibus', $message, $min_price, $lowest_price_date);
    }

    /**
     * Show minimum price message using separate event
     * @return string|void
     */
    public static function separateOmnibusMessageForDiscountRule() {

        $is_eligible = self::$helper->checkRuleId();
        if(isset($is_eligible) && empty($is_eligible)){
            return '';
        }

        $min_price = self::$helper->omnibusForDiscountRules();
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $custom_message = get_option('_awdr_om_message');
            $message = isset($custom_message) && !empty($custom_message)? $custom_message : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('advanced_woo_discount_rules_omnibus_directive_message_date_format',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
        } else {
            return '';
        }
        $message = apply_filters('advanced_woo_discount_rules_omnibus_directive_message', $message, $min_price, $lowest_price_date);
        _e($message, 'wdr-omnibus-directive');
    }

    /**
     * Show the lowest price in product edit page
     * @return void
     */
    public static function showLowestPriceInProductEditPage() {

        global $post;
        $id = $post->ID;
        $awdr_price_history = get_post_meta($id, '_awdr_price_history', true);

        if(!empty($awdr_price_history) && is_array($awdr_price_history)){
            $prices = array_column($awdr_price_history, 'price');
            $price_lowest = min($prices);

            foreach ($awdr_price_history as $awdr_price_history_data) {
                if($awdr_price_history_data['price'] == $price_lowest){
                    $timestamp = $awdr_price_history_data['timestamp'];
                }
            }
        }

        if(isset($price_lowest) && is_numeric($price_lowest) && isset($timestamp) && is_numeric($timestamp)) {
            self::$helper->print_header('description');
            self::$helper->woocommerce_wp_text_input_price($price_lowest);
            self::$helper->woocommerce_wp_text_input_date($timestamp);
        }
    }

    /**
     * Update omnibus add-on setting tab settings data
     * @return void
     */
    public static function saveSettingsData() {
        if(isset($_POST['submit'])) {
            $updated_days = isset($_POST['awdr_refresh_date']) && is_numeric($_POST['awdr_refresh_date']) && $_POST['awdr_refresh_date'] >= 30 ? $_POST['awdr_refresh_date'] : 30;
            $show_omnibus_message_option = isset($_POST['show_omnibus_message_option']) && is_numeric($_POST['show_omnibus_message_option']) ? $_POST['show_omnibus_message_option'] : 0;
            $message = isset($_POST['awdr_om_message']) ? sanitize_textarea_field($_POST['awdr_om_message']) : null;
            $is_override_omnibus_message = isset($_POST['is_override_omnibus_message']) ? $_POST['is_override_omnibus_message'] : 0;
            $selected_rules = isset($_POST['selected_rules']) && is_array($_POST['selected_rules']) ? $_POST['selected_rules'] : array();
            $position_to_show_message = isset($_POST['position_to_show_message']) ? $_POST['position_to_show_message'] : 'woocommerce_before_single_product_summary';

            update_option('_awdr_price_lowest_days',$updated_days);
            update_option('_awdr_show_omnibus_message',$show_omnibus_message_option);
            update_option('_awdr_om_message',$message);
            update_option('_is_override_omnibus_message',$is_override_omnibus_message);
            update_option('_awdr_om_selected_rules',$selected_rules);
            update_option('_awdr_position_to_show_message',$position_to_show_message);
        }
    }

    /**
     * To load the script files
     * @return void
     */
    public static function scriptFiles() {
        wp_enqueue_style('wdr_od_add_css', trailingslashit(WDR_OD_PLUGIN_URL) . 'assets/Css/style.css');
        wp_enqueue_script('wdr_od_add_js',trailingslashit(WDR_OD_PLUGIN_URL) . 'assets/Js/index.js',array('jquery'));
    }

    /**
     * Add settings link in plugins page
     * @param $links
     * @return string[]
     */
    public static function wdrOmActionLink($links) {
        $action_links = array(
            'settings' => '<a href="' . esc_url(admin_url('admin.php?page=woo_discount_rules&tab=addons&addon=omnibus_directive&section=settings')) . '">' . __('Settings', 'woo-discount-rules') . '</a>',
        );
        return array_merge($action_links, $links);
    }
}