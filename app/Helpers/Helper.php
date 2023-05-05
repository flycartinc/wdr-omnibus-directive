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

namespace WDR_OD\App\Helpers;
use WDR_OD\App\Controllers\Admin\OmnibusAddon;

defined('ABSPATH') or exit;

class Helper {

    public $date;

    /**
     * Get and update minimum price
     * @return int|mixed
     */
    public function getAndUpdateMinimumPrice() {

        global $product;
        $product_id = $product->get_id();
        $settings_data = get_option('wdr_omnibus_directive');
        $number_of_days = isset($settings_data['number_of_days']) ? $settings_data['number_of_days'] : 30;

        $sale_price = $product->get_price();
        if ($product->get_type() == 'variable') {
            $discount_price = array();
            $min_discounted_price = 0;
            $available_variations = $product->get_variation_prices();
            foreach ($available_variations['regular_price'] as $key => $regular_price) {
                if (function_exists('wc_get_product')) {
                    $product_variation = wc_get_product($key);
                    $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product_variation, 1, 0, 'discounted_price', true, false);
                    if (isset($discount) && $discount !== false) {
                        $discount_price[] = $discount;
                        $min_discounted_price = (min($discount_price));
                    }
                }
            }
            $discount = $min_discounted_price;
        } else {
            $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', $sale_price, $product, 1, 0, 'discounted_price', true, false);
        }

        $wdr_od_price_current = get_post_meta($product_id, '_wdr_od_price_current', true);
        $wdr_od_price_history = get_post_meta($product_id, '_wdr_od_price_history', true);

        // Update the current price in _wdr_od_price_current meta key
        if (!empty($discount) && empty($wdr_od_price_current)) {
            $wdr_od_price_current_update = [
                'price' => $discount,
                'timestamp' => current_time('timestamp', true),
            ];
            update_post_meta($product_id, '_wdr_od_price_current', $wdr_od_price_current_update);
        }

        if(!empty($wdr_od_price_current)) {
            $current_price_time_difference = current_time('timestamp', true) - $wdr_od_price_current['timestamp'];
            if ($current_price_time_difference > $number_of_days * 24 * 60 * 60) {
                delete_post_meta($product_id, '_wdr_od_price_current');
            }
        }

        if(empty($wdr_od_price_history)){
            $wdr_od_price_history = array();
        }

        foreach ( $wdr_od_price_history as $key => $wdr_od_price_history_data ) {
            $history_price_time_difference = current_time('timestamp', true) - $wdr_od_price_history_data['timestamp'];
            if($history_price_time_difference > $number_of_days * 24 * 60 * 60 ) { //$number_of_days * 24 * 60 * 60
                unset($wdr_od_price_history[$key]);
                update_post_meta($product_id, '_wdr_od_price_history', $wdr_od_price_history);
            }
        }

        // Update the price history in _wdr_od_price_history meta key
        if (!empty($discount) && !empty($wdr_od_price_current['price']) && $discount < $wdr_od_price_current['price']) {

            $wdr_od_price_history_update = [
                'price' => $wdr_od_price_current['price'],
                'timestamp' => current_time('timestamp', true),
            ];

            $wdr_od_price_current_update = [
                'price' => $discount,
                'timestamp' => current_time('timestamp', true),
            ];

            $wdr_od_price_history[] = $wdr_od_price_history_update;
            sort($wdr_od_price_history);

            update_post_meta($product_id, '_wdr_od_price_current', $wdr_od_price_current_update);
            update_post_meta($product_id, '_wdr_od_price_history', $wdr_od_price_history);
        }

        if(!empty($wdr_od_price_history) && is_array($wdr_od_price_history)){
            $prices = array_column($wdr_od_price_history, 'price');
            $min_price = min($prices);
        }

        foreach ($wdr_od_price_history as $wdr_od_price_history_data) {
            if(isset($min_price) && $wdr_od_price_history_data['price'] == $min_price){
                $this->date = $wdr_od_price_history_data['timestamp'];
                break;
            }
        }
        return apply_filters('wdr_omnibus_directive_min_price', isset($min_price) ? $min_price : 0 );
    }

    /**
     * Header for lowest price display field in product edit page
     * @param $class
     * @return void
     */
    public static function headerForShowLowestPriceInProductEditPage($class = '') {
        printf(
            '<h3%s>%s</h3>',
            empty($class) ? '' : sprintf(' class="%s"', esc_attr($class)),
            esc_html__('Discount rules lowest preview price', 'wdr-omnibus-directive')
        );
    }

    /**
     * Price input field for show the lowest price of the product in product edit page
     * @param $price_lowest
     * @param $number_of_days
     * @param $configuration
     * @return void
     */
    public static function showLowestPreviewPriceInProductEditPage($price_lowest, $number_of_days, $configuration = array()) {
        woocommerce_wp_text_input(
            wp_parse_args(
                array(
                    'id'                => 'wdr-od-price-history-price',
                    'custom_attributes' => array('disabled' => 'disabled'),
                    'value'             => empty($price_lowest) ? __('no data', 'wdr-omnibus-directive') : $price_lowest,
                    'data_type'         => 'price',
                    'label'             => __('Price','wdr-omnibus-directive').'('.get_woocommerce_currency_symbol().')',
                    'desc_tip'          => true,
                    'description'       => sprintf(
                        __('The lowest price in %d days.','wdr-omnibus-directive'),
                        $number_of_days
                    ),
                ),
                $configuration
            )
        );
    }

    /**
     * Date input field for show the lowest price edit date in product edit page
     * @param $timestamp
     * @param $number_of_days
     * @param $configuration
     * @return void
     */
    public static function showLowestPreviewPriceDateInProductEditPage($timestamp, $number_of_days, $configuration = array()) {
        woocommerce_wp_text_input(
            wp_parse_args(
                array(
                    'id'                => 'wdr-od-price-history-date',
                    'custom_attributes' => array('disabled' => 'disabled'),
                    'value'             => empty($timestamp) ? esc_html__('no data', 'wdr-omnibus-directive') : date_i18n(get_option('date_format'),$timestamp),
                    'data_type'         => 'text',
                    'label'             => __('Date','wdr-omnibus-directive'),
                    'desc_tip'          => true,
                    'description'       => sprintf(
                        __('The date when lowest price in %d days occurred.', 'wdr-omnibus-directive'),
                        $number_of_days
                    ),
                ),
                $configuration
            )
        );
    }

    /**
     * Add omnibus addon in discount rules
     * @param $addons
     * @return mixed
     */
    public static function omnibusAddon($addons) {
        $addons['omnibus_directive'] = new OmnibusAddon();
        return $addons;
    }

    /**
     * Check the rule is eligible or not
     * @return bool
     */
    public static function checkRuleId() {
        global $product;
        $discount = false;
        if ($product->get_type() == 'variable') {
            $available_variations = $product->get_variation_prices();
            foreach ($available_variations['regular_price'] as $key => $regular_price) {
                if (function_exists('wc_get_product')) {
                    $product_variation = wc_get_product($key);
                    $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product_variation, 1, 0, 'all', true, false);
                }
            }
        } else {
            $price = $product->get_price();
            $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', $price, $product, 1, 0, 'all', true, false);
        }

        if($discount !== false){
            if(class_exists('\Wdr\App\Controllers\DiscountCalculator')) {
                if(isset($discount['total_discount_details']) && !empty($discount['total_discount_details'])){
                    $rules = \Wdr\App\Controllers\DiscountCalculator::$rules;
                    $rule_ids = array_keys($discount['total_discount_details']);
                    $settings_data = get_option('wdr_omnibus_directive');
                    $selected_rules = isset($settings_data['selected_rules']) && !empty($settings_data['selected_rules']) && is_array($settings_data['selected_rules']) ? $settings_data['selected_rules'] : array();;
                    foreach ($rule_ids as $rule_id) {
                        if(isset($rules[$rule_id])) {
                            $matched_rule = $rules[$rule_id]->rule; // Here we get the matched rule info
                            if( $matched_rule->enabled == 1 &&  in_array($matched_rule->id,$selected_rules) && $matched_rule->discount_type == "wdr_simple_discount" ){
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Check Omnibus plugin active or not
     * @return bool
     */
    public function isOmnibusPluginActive() {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('omnibus/omnibus.php', $active_plugins, false) || array_key_exists('omnibus/omnibus.php', $active_plugins);
    }

    /**
     * Check the enabled product adjustment rules
     * @return array
     */
    public function checkRuleEnabled() {
        $check_enabled_rules = array();
        if (class_exists('\Wdr\App\Controllers\ManageDiscount')) {
            $rules = \Wdr\App\Controllers\ManageDiscount::$available_rules;
            $settings_data = get_option('wdr_omnibus_directive');
            $selected_rules = isset($settings_data['selected_rules']) && !empty($settings_data['selected_rules']) && is_array($settings_data['selected_rules']) ? $settings_data['selected_rules'] : array();
            foreach ($rules as $available_rule) {
                $selected = in_array($available_rule->rule->id, $selected_rules) ? "selected" : "";
                if ($available_rule->rule->discount_type == 'wdr_simple_discount' && $available_rule->rule->enabled == 1) {
                    $check_enabled_rules[] = [
                        'selected' => $selected,
                        'rule_id' => $available_rule->rule->id,
                        'rule_title' => $available_rule->rule->title,
                    ];
                }
            }
        }
        return $check_enabled_rules;
    }

    /**
     * Check allowed html tags before save
     * @param $message_without_filter
     * @return string
     */
    public function checkMessageTags($message_without_filter) {
        $allowed_tags = array(
            'p' => array('class' => array(), 'style' => array()),
            'span' => array('class' => array(), 'style' => array()),
            'div' => array('class' => array(), 'style' => array()),
            'h4' => array('class' => array()),
            'h3' => array('class' => array()),
            'h1' => array('class' => array()),
            'h2' => array('class' => array()),
            'strong' => array(),
            'i' => array()
        );
        $allowed_tags = apply_filters( 'wdr_omnibus_directive_allowed_html_elements_and_attributes', $allowed_tags);
        return wp_kses($message_without_filter, $allowed_tags);
    }

    /**
     * Change discount rules price html priority
     * @return void
     */
    public function changeDiscountRulesPriceHtmlPriority() {
        if(class_exists('\Wdr\App\Router')){
            remove_filter('woocommerce_get_price_html', array(\Wdr\App\Router::$manage_discount, 'getPriceHtml'), 100, 2);
            add_filter('woocommerce_get_price_html', array(\Wdr\App\Router::$manage_discount, 'getPriceHtml'), 9, 2);
            remove_filter('woocommerce_variable_price_html', array(\Wdr\App\Router::$manage_discount, 'getVariablePriceHtml'), 100);
            add_filter('woocommerce_variable_price_html', array(\Wdr\App\Router::$manage_discount, 'getVariablePriceHtml'), 9, 2);
        }
    }
}