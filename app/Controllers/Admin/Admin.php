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
use Wdr\App\Helpers\Rule;
use Wdr\App\Helpers\Woocommerce;
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

        global $product;
        $is_eligible = self::$helper->checkRuleId($product);
        if(isset($is_eligible) && empty($is_eligible)){
            return $message;
        }

        $min_price = self::$helper->getAndUpdateMinimumPrice($product);
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $settings_data = get_option('wdr_omnibus_directive');
            $message = isset($settings_data['message']) && !empty($settings_data['message']) ? $settings_data['message'] : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('wdr_omnibus_directive_message_date_format_for_omnibus',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
        }
        return apply_filters('wdr_omnibus_directive_merge_omnibus_message_with_discount_rule', $message, $min_price, $lowest_price_date);
    }

    /**
     * Change omnibus price lowest for woocommerce_get_price_html event
     * @param $price_lowest
     * @param $product
     * @return mixed|null
     */
    public static function changeOmnibusPriceLowest($price_lowest, $product) {

        $is_eligible = self::$helper->checkRuleId($product);
        if(isset($is_eligible) && empty($is_eligible)){
            return $price_lowest;
        }

        self::$helper->getAndUpdateMinimumPrice($product);
        $product_id = (int)Woocommerce::getProductId($product);
        if(empty($product_id)) {
            return $price_lowest;
        }
        $wdr_od_price_history = get_post_meta($product_id, '_wdr_od_price_history', true);
        if(!empty($wdr_od_price_history) && is_array($wdr_od_price_history)){
            $prices = array_column($wdr_od_price_history, 'price');
            $min_price = min($prices);
        }
        $min_price = isset($min_price) ? $min_price : 0;
        $date = self::$helper->date;
        $date = isset($date) ? $date : 0;

        $price_lowest['price'] = $min_price;
        $price_lowest['timestamp'] = $date;
        $price_lowest['price_including_tax'] = null;

        return apply_filters('wdr_omnibus_directive_change_omnibus_price_lowest', $price_lowest, $min_price, $date);
    }

    /**
     * Change omnibus message for woocommerce_get_price_html template
     * @param $message
     * @return mixed|null
     */
    public static function changeOmnibusMessageTemplate($message) {
        $settings_data = get_option('wdr_omnibus_directive');
        $message = isset($settings_data['message']) && !empty($settings_data['message']) ? $settings_data['message'] : "Preview lowest price was {{price}} updated from {{date}}";
        $message = str_replace('{{price}}', '{price}', $message);
        $message = str_replace('{{date}}', date_i18n(get_option('date_format'),"{timestamp}"), $message);

        return apply_filters('wdr_omnibus_directive_change_omnibus_message_template', $message);
    }

    /**
     * Show message using separate event
     * @return string|void
     */
    public static function separateOmnibusMessageForDiscountRule() {
        global $product;
        $is_eligible = self::$helper->checkRuleId($product);
        if(isset($is_eligible) && empty($is_eligible)){
            return '';
        }

        $min_price = self::$helper->getAndUpdateMinimumPrice($product);
        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $settings_data = get_option('wdr_omnibus_directive');
            $message = isset($settings_data['message']) && !empty($settings_data['message']) ? $settings_data['message'] : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('wdr_omnibus_directive_message_date_format',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
            $message = '<div class="wdr-od-message">' . $message . '</div>';
        } else {
            return '';
        }
        $message = apply_filters('wdr_omnibus_directive_separate_omnibus_message', $message, $min_price, $lowest_price_date);
        _e($message, 'wdr-omnibus-directive');
    }

    /**
     * Show message using separate event for woocommerce_get_price_html hook
     * @param $price
     * @param $product
     * @return mixed|string
     */
    public static function separateGetPriceHtmlOmnibusMessage($price, $product) {

        if(!is_product()) {
            return $price;
        }
        $is_eligible = self::$helper->checkRuleId($product);
        if(isset($is_eligible) && empty($is_eligible)){
            return $price;
        }

        self::$helper->getAndUpdateMinimumPrice($product);
        $product_id = (int)Woocommerce::getProductId($product);
        if(empty($product_id)) {
            return $price;
        }
        $wdr_od_price_history = get_post_meta($product_id, '_wdr_od_price_history', true);
        if(!empty($wdr_od_price_history) && is_array($wdr_od_price_history)){
            $prices = array_column($wdr_od_price_history, 'price');
            $min_price = min($prices);
        }

        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $settings_data = get_option('wdr_omnibus_directive');
            $message = isset($settings_data['message']) && !empty($settings_data['message']) ? $settings_data['message'] : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('wdr_omnibus_directive_message_date_format',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
            $message = '<div class="wdr-od-message">' . $message . '</div>';
        } else {
            $message = null;
            $min_price = 0;
        }

        $message = apply_filters('wdr_omnibus_directive_separate_get_price_html_message', $message, $min_price, $lowest_price_date);
        return $price . $message;
    }

    /**
     * Show message using separate event for dynamic price html
     * @param $price_html
     * @param $product
     * @param $awdr_request
     * @return mixed|string
     */
    public static function separateDynamicPriceHtmlOmnibusMessage($price_html, $product, $awdr_request) {

        $is_eligible = self::$helper->checkRuleId($product);
        if(isset($is_eligible) && empty($is_eligible)){
            return $price_html;
        }

        self::$helper->getAndUpdateMinimumPrice($product);
        $product_id = (int)Woocommerce::getProductId($product);
        if(empty($product_id)) {
            return $price_html;
        }
        $wdr_od_price_history = get_post_meta($product_id, '_wdr_od_price_history', true);
        if(!empty($wdr_od_price_history) && is_array($wdr_od_price_history)){

            $prices = array_column($wdr_od_price_history, 'price');
            $min_price = min($prices);
        }

        $date = self::$helper->date;
        $lowest_price_date = isset($date) && !empty($date)? $date : 0;
        if (!empty($min_price)) {
            $settings_data = get_option('wdr_omnibus_directive');
            $message = isset($settings_data['message']) && !empty($settings_data['message']) ? $settings_data['message'] : "Preview lowest price was {{price}} updated from {{date}}";
            $message = str_replace('{{price}}', wc_price($min_price), $message);
            $date_format = apply_filters('wdr_omnibus_directive_message_date_format',date_i18n(get_option('date_format'),$lowest_price_date), $lowest_price_date, $min_price);
            $message = str_replace('{{date}}', $date_format, $message);
            $message = '<div class="wdr-od-message">' . $message . '</div>';
        } else {
            $message = null;
            $min_price = 0;
        }

        $message = apply_filters('wdr_omnibus_directive_separate_dynamic_price_html_message', $message, $min_price, $lowest_price_date);
        return $price_html . $message;
    }

    /**
     * Discount rule dynamic price html compatibility
     * @param $price_html
     * @param $product
     * @param $awdr_request
     * @return string
     */
    public static function DynamicPriceHtmlForOmnibusCompatible($price_html, $product, $awdr_request) {
        ob_start();
        $product_id = (int)Woocommerce::getProductId($product);
        if(empty($product_id)) {
            return $price_html;
        }
        do_action( 'iworks_omnibus_wc_lowest_price_message', $product_id);
        return $price_html . ob_get_clean();
    }

    /**
     * Show the lowest price in product edit page
     * @return void
     */
    public static function showLowestPriceInProductEditPage() {

        global $post;
        $id = $post->ID;
        $price_history = get_post_meta($id, '_wdr_od_price_history', true);
        $price_lowest = 0;
        $timestamp = 0;
        if(!empty($price_history) && is_array($price_history)){
            $prices = array_column($price_history, 'price');
            $price_lowest = min($prices);

            foreach ($price_history as $price_history_data) {
                if($price_history_data['price'] == $price_lowest){
                    $timestamp = $price_history_data['timestamp'];
                }
            }
        }

        $settings_data = get_option('wdr_omnibus_directive');
        $number_of_days = isset($settings_data['number_of_days']) ? $settings_data['number_of_days'] : 30;

        self::$helper->headerForShowLowestPriceInProductEditPage('description');
        self::$helper->showLowestPreviewPriceInProductEditPage($price_lowest, $number_of_days);
        self::$helper->showLowestPreviewPriceDateInProductEditPage($timestamp, $number_of_days);
    }

    /**
     * Update omnibus add-on setting tab settings data
     * @return void
     */
    public static function saveSettingsData() {
        if(isset($_POST['wdr-od-submit'])) {
            if (wp_verify_nonce($_POST['wdr_od_nonce_name'], 'wdr_od_nonce_action')) {

                $number_of_days = $_POST['wdr-od-number-of-days'];
                $is_show_omnibus_message_option = $_POST['wdr-od-is-show-message-option'];
                $message = $_POST['wdr-od-message'];
                $is_override_omnibus_message = $_POST['wdr-od-is-override-omnibus-message'];
                $selected_rules = $_POST['wdr-od-selected_rules'];
                $position_to_show_message = $_POST['wdr-od-position-to-show-message'];

                $acceptable = array('1','0');
                $settings_data = [
                    'number_of_days' => isset($number_of_days) && is_numeric($number_of_days) && $number_of_days >= 30 ? round($number_of_days) : 30,
                    'is_show_omnibus_message_option' => isset($is_show_omnibus_message_option) && in_array($is_show_omnibus_message_option, $acceptable,true) ? $is_show_omnibus_message_option : 0,
                    'message' => Rule::validateHtmlBeforeSave(isset($message) ? trim($message) : null),
                    'is_override_omnibus_message' => isset($is_override_omnibus_message) && in_array($is_override_omnibus_message, $acceptable,true) ? $is_override_omnibus_message : 0,
                    'selected_rules' => isset($selected_rules) && is_array($selected_rules) ? $selected_rules : array(),
                    'position_to_show_message' => isset($position_to_show_message) ? sanitize_text_field($position_to_show_message) : 'woocommerce_single_product_summary',
                ];
                update_option('wdr_omnibus_directive',$settings_data);
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
        if(isset($_GET['page']) && isset($_GET['tab']) && $_GET['page'] == 'woo_discount_rules' && $_GET['tab'] == 'addons') {
            wp_enqueue_script('wdr_od_add_js',trailingslashit(WDR_OD_PLUGIN_URL) . 'assets/Js/index.js',array('jquery'), WDR_OD_VERSION);
        }
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
        $message = __('Saved successfully.', 'wdr-omnibus-directive');
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    /**
     * Settings save failed message
     * @return void
     */
    function errorNotice() {
        $class = 'notice notice-error';
        $message = __('Error occurred.', 'wdr-omnibus-directive');
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
}