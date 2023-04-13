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

        $min_price = self::$helper->getAndUpdateMinimumPrice();
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $custom_message = get_option('_wdr_od_message');
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

        $min_price = self::$helper->getAndUpdateMinimumPrice();
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $custom_message = get_option('_wdr_od_message');
            $message = isset($custom_message) && !empty($custom_message)? $custom_message : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('advanced_woo_discount_rules_omnibus_directive_message_date_format',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
            $message = '<div class="wdr-od-message">' . $message . '</div>';
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
        $price_history = get_post_meta($id, '_wdr_od_price_history', true);

        if(!empty($price_history) && is_array($price_history)){
            $prices = array_column($price_history, 'price');
            $price_lowest = min($prices);

            foreach ($price_history as $price_history_data) {
                if($price_history_data['price'] == $price_lowest){
                    $timestamp = $price_history_data['timestamp'];
                }
            }
        }

        if(isset($price_lowest) && is_numeric($price_lowest) && isset($timestamp) && is_numeric($timestamp)) {
            $number_of_days = get_option('_wdr_od_number_of_days');
            self::$helper->headerForShowLowestPriceInProductEditPage('description');
            self::$helper->showLowestPreviewPriceInProductEditPage($price_lowest, $number_of_days);
            self::$helper->showLowestPreviewPriceDateInProductEditPage($timestamp, $number_of_days);
        }
    }

    /**
     * Update omnibus add-on setting tab settings data
     * @return void
     */
    public static function saveSettingsData() {
        if(isset($_POST['wdr-od-submit'])) {
            if (wp_verify_nonce($_POST['wdr_od_nonce_name'], 'wdr_od_nonce_action')) {
                $updated_days = isset($_POST['wdr-od-number-of-days']) && is_numeric($_POST['wdr-od-number-of-days']) && $_POST['wdr-od-number-of-days'] >= 30 ? $_POST['wdr-od-number-of-days'] : 30;
                $show_omnibus_message_option = isset($_POST['wdr-od-is-show-message-option']) && is_numeric($_POST['wdr-od-is-show-message-option']) ? $_POST['wdr-od-is-show-message-option'] : 0;
                $message = isset($_POST['wdr_od_message']) ? sanitize_textarea_field($_POST['wdr_od_message']) : null;
                $is_override_omnibus_message = isset($_POST['wdr-od-is-override-omnibus-message']) && is_numeric($_POST['wdr-od-is-override-omnibus-message']) ? $_POST['wdr-od-is-override-omnibus-message'] : 0;
                $selected_rules = isset($_POST['wdr-od-selected_rules']) && is_array($_POST['wdr-od-selected_rules']) ? $_POST['wdr-od-selected_rules'] : array();
                $position_to_show_message = isset($_POST['wdr-od-position-to-show-message']) ? sanitize_text_field($_POST['wdr-od-position-to-show-message']) : 'woocommerce_single_product_summary';

                update_option('_wdr_od_number_of_days', $updated_days);
                update_option('_wdr_od_is_show_omnibus_message', $show_omnibus_message_option);
                update_option('_wdr_od_message', $message);
                update_option('_wdr_od_is_override_omnibus_message', $is_override_omnibus_message);
                update_option('_wdr_od_selected_rules', $selected_rules);
                update_option('_wdr_od_position_to_show_message', $position_to_show_message);

                wp_safe_redirect(add_query_arg('saved', 'true'));
            } else {
                wp_safe_redirect(add_query_arg('saved', 'false'));
            }
            exit();
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
            'settings' => '<a href="' . esc_url(admin_url('admin.php?page=woo_discount_rules&tab=addons&addon=omnibus_directive&section=settings')) . '">' . __('Settings', 'wdr-omnibus-directive') . '</a>',
        );
        return array_merge($action_links, $links);
    }

    /**
     * Settings save successfully message
     * @return void
     */
    function successNotice() {
        $class = 'notice notice-success';
        $message = __( 'Saved successfully.', 'sample-text-domain' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    /**
     * Settings save failed message
     * @return void
     */
    function errorNotice() {
        $class = 'notice notice-error';
        $message = __( 'Error occurred.', 'sample-text-domain' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
}