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
     * Show minimum price message using omnibus event
     * @param $message
     * @param $price
     * @param $price_lowest
     * @return mixed|string
     */
    public static function mergeOmnibusMessageWithDiscountRule($message, $price, $price_lowest) {

        $is_eligible = Helper::checkRuleId();
        if(isset($is_eligible) && empty($is_eligible)){
            return $message;
        }
        $min_price = Helper::omnibusForDiscountRules();
        if (!empty($min_price)) {
            $message = "Previous lowest price was (from awdr)"." ".wc_price($min_price);
        }
        return $message;
    }

    /**
     * Show minimum price message using separate event
     * @return string|void
     */
    public static function separateOmnibusMessageForDiscountRule() {

        $is_eligible = Helper::checkRuleId();
        if(isset($is_eligible) && empty($is_eligible)){
            return '';
        }
        $helper = new Helper();
        $min_price = $helper->omnibusForDiscountRules();
        $date = $helper->date;
        if (!empty($min_price)) {
            $custom_message = get_option('_awdr_om_message');
            $message = isset($custom_message) && !empty($custom_message)? $custom_message : "Preview lowest price was {{price}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $lowest_price_date = isset($date) && !empty($date)? $date : 0;
            $message = str_replace('{{date}}', date_i18n(get_option('date_format'),$lowest_price_date), $message);
        } else {
            return '';
        }
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
            Helper::print_header('description');
            Helper::woocommerce_wp_text_input_price($price_lowest);
            Helper::woocommerce_wp_text_input_date($timestamp);
        }
    }

    /**
     * Update omnibus add-on setting tab settings data
     * @return void
     */
    public static function saveSettingsData() {
        if(isset($_POST['submit']) && is_numeric($_POST['awdr_refresh_date']) && is_numeric($_POST['show_omnibus_message_option']) && $_POST['awdr_refresh_date'] >= 30) {
            $updated_days = $_POST['awdr_refresh_date'];
            $show_omnibus_message_option = $_POST['show_omnibus_message_option'];
            $message = $_POST['awdr_om_message'];
            $selected_rules = $_POST['selected_rules'];
            $position_to_show_message = $_POST['position_to_show_message'];

            update_option('_awdr_price_lowest_days',$updated_days );
            update_option('_awdr_show_omnibus_message',$show_omnibus_message_option );
            update_option('_awdr_om_message',$message );
            update_option('_awdr_om_selected_rules',$selected_rules );
            update_option('_awdr_position_to_show_message',$position_to_show_message );
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
}