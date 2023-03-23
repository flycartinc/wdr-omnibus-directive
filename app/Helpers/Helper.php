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

class Helper {

    public $date;

    /**
     * Get and update minimum price
     * @return int|mixed
     */
    public function omnibusForDiscountRules() {

        global $product;
        $product_id = $product->get_id();
        $awdr_days = get_option('_awdr_price_lowest_days');

        $sale_price = $product->get_price();
        $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', $sale_price, $product, 1, 0, 'discounted_price', true, false);

        $awdr_price_current = get_post_meta($product_id, '_awdr_price_current', true);
        $awdr_price_history = get_post_meta($product_id, '_awdr_price_history', true);

        // Update the current price in _awdr_price_current meta key
        if (!empty($discount) && empty($awdr_price_current)) {
            $awdr_price_current_update = [
                'price' => $discount,
                'timestamp' => time(),
            ];
            update_post_meta($product_id, '_awdr_price_current', $awdr_price_current_update);
        }

        if(!empty($awdr_price_current)) {
            $current_price_time_difference = time() - $awdr_price_current['timestamp'];
            if ($current_price_time_difference > $awdr_days * 24 * 60 * 60) {
                delete_post_meta($product_id, '_awdr_price_current');
            }
        }

        if(empty($awdr_price_history)){
            $awdr_price_history = array();
        }

        foreach ( $awdr_price_history as $key => $awdr_price_history_data ) {
            $history_price_time_difference = time() - $awdr_price_history_data['timestamp'];
            if($history_price_time_difference > $awdr_days * 24 * 60 * 60 ) { //$awdr_days * 24 * 60 * 60
                unset($awdr_price_history[$key]);
                update_post_meta($product_id, '_awdr_price_history', $awdr_price_history);
            }
        }

        // Update the price history in _awdr_price_history meta key
        if (!empty($discount) && !empty($awdr_price_current['price']) && $discount < $awdr_price_current['price']) {

            $awdr_price_history_update = [
                'price' => $awdr_price_current['price'],
                'timestamp' => time(),
            ];

            $awdr_price_current_update = [
                'price' => $discount,
                'timestamp' => time(),
            ];

            $awdr_price_history[] = $awdr_price_history_update;
            sort($awdr_price_history);

            update_post_meta($product_id, '_awdr_price_current', $awdr_price_current_update);
            update_post_meta($product_id, '_awdr_price_history', $awdr_price_history);
        }

        if(!empty($awdr_price_history) && is_array($awdr_price_history)){
            $prices = array_column($awdr_price_history, 'price');
            $min_price = min($prices);
        }

        foreach ($awdr_price_history as $awdr_price_history_data) {
            if(isset($min_price) && $awdr_price_history_data['price'] == $min_price){
                $this->date = $awdr_price_history_data['timestamp'];
                break;
            }
        }

        return isset($min_price) ? $min_price : 0;

//            option to show price when set the discount at first time

//            $first_price = 1;
//            if($first_price == 1 && empty($awdr_price_history) && !empty($awdr_price_current) && is_array($awdr_price_current)){
//                $message = "Previous lowest price was (from awdr test)"." ".wc_price($awdr_price_current['price']);
//            }

    }

    /**
     * Header for lowest price display field in product edit page
     * @param $class
     * @return void
     */
    public static function print_header($class = '') {
        printf(
            '<h3%s>%s</h3>',
            empty($class) ? '' : sprintf(' class="%s"', esc_attr($class)),
            esc_html__('Discount rules lowest preview price', 'wdr-omnibus-directive')
        );
    }

    /**
     * Price input field for show the lowest price of the product in product edit page
     * @param $price_lowest
     * @param $configuration
     * @return void
     */
    public static function woocommerce_wp_text_input_price($price_lowest, $configuration = array()) {
        $awdr_days = get_option('_awdr_price_lowest_days');
        woocommerce_wp_text_input(
            wp_parse_args(
                array(
                    'id'                => 'awdr_price_history_price',
                    'custom_attributes' => array('disabled' => 'disabled'),
                    'value'             => empty($price_lowest) ? __('no data', 'wdr-omnibus-directive') : $price_lowest,
                    'data_type'         => 'price',
                    'label'             => __('Price','wdr-omnibus-directive').'('.get_woocommerce_currency_symbol().')',
                    'desc_tip'          => true,
                    'description'       => sprintf(
                        __('The lowest price in %d days.','wdr-omnibus-directive'),
                        $awdr_days
                    ),
                ),
                $configuration
            )
        );
    }

    /**
     * Date input field for show the lowest price edit date in product edit page
     * @param $timestamp
     * @param $configuration
     * @return void
     */
    public static function woocommerce_wp_text_input_date($timestamp, $configuration = array()) {
        $awdr_days = get_option('_awdr_price_lowest_days');
        woocommerce_wp_text_input(
            wp_parse_args(
                array(
                    'id'                => 'awdr_price_history_date',
                    'custom_attributes' => array('disabled' => 'disabled'),
                    'value'             => empty($timestamp) ? esc_html__('no data', 'wdr-omnibus-directive') : date_i18n(get_option('date_format'),$timestamp),
                    'data_type'         => 'text',
                    'label'             => __('Date','wdr-omnibus-directive'),
                    'desc_tip'          => true,
                    'description'       => sprintf(
                        __('The date when lowest price in %d days occurred.', 'wdr-omnibus-directive'),
                        $awdr_days
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
        $price = $product->get_price();
        $discount = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', $price, $product, 1, 0, 'all', true, false);
        if($discount !== false){
            if(class_exists('\Wdr\App\Controllers\DiscountCalculator')) {
                if(isset($discount['total_discount_details']) && !empty($discount['total_discount_details'])){
                    $rules = \Wdr\App\Controllers\DiscountCalculator::$rules;
                    $rule_ids = array_keys($discount['total_discount_details']);
                    $selected_rules = !empty(get_option('_awdr_om_selected_rules')) ? get_option('_awdr_om_selected_rules') : array();
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
}