<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<br>
<div>
    <form method="post">
        <h1><?php _e('Omnibus Directive : General setting', 'wdr-omnibus-directive') ?></h1>
        <table class="wdr-general-setting form-table">
            <tbody style="background-color: #fff;">
            <tr>
                <td>
                    <label class="awdr-left-align"><?php _e('Number of days', 'wdr-omnibus-directive') ?></label>
                    <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Record and display number of days after sale was started', 'wdr-omnibus-directive'); ?></span>
                </td>
                <td>
                    <input type="number" name="awdr_refresh_date" value="<?php echo !empty($number_of_days) ? $number_of_days : 30;?>" title="Qty" size="4" min="30" max="" step="1" >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="awdr-left-align"><?php _e('Show Omnibus message on product page', 'wdr-omnibus-directive') ?></label>
                    <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Hide the message from omnibus plugin', 'wdr-omnibus-directive'); ?></span>
                </td>
                <td>
                    <input type="radio" name="show_omnibus_message_option"
                           value="1"
                        <?php echo(!empty($show_omnibus_message) ? 'checked' : '')  ?>><label
                    ><?php _e('Yes', 'wdr-omnibus-directive'); ?></label>

                    <input type="radio" name="show_omnibus_message_option"
                           value="0"
                        <?php echo(empty($show_omnibus_message) ? 'checked' : '') ?>><label
                    ><?php _e('No', 'wdr-omnibus-directive'); ?></label>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="" class="awdr-left-align"><?php _e('Select rules', 'wdr-omnibus-directive') ?></label>
                    <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Select the product adjustment rules', 'wdr-omnibus-directive'); ?></span>
                </td>
                <td>
                    <select class="awdr-om-select-rules" data-placeholder="<?php esc_attr_e(" Select rules here ", 'wdr-omnibus-directive');?>" name="selected_rules[]" multiple="true" >
                        <?php
                        if(class_exists('\Wdr\App\Controllers\ManageDiscount')){
                            $rules = \Wdr\App\Controllers\ManageDiscount::$available_rules;
                            $selected_rules = !empty(get_option('_awdr_om_selected_rules')) ? get_option('_awdr_om_selected_rules') : array();
                            foreach ($rules as $available_rule){
                                $selected = in_array($available_rule->rule->id,$selected_rules) ? "selected" : "" ;
                                $discount_type = $available_rule->rule->discount_type;
                                if($available_rule->rule->discount_type == 'wdr_simple_discount' && $available_rule->rule->enabled == 1 ){
                                    echo '<option  '.$selected.'  value="'.$available_rule->rule->id.'">' . $available_rule->rule->title . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="awdr-left-align"><?php _e('Position to show message', 'wdr-omnibus-directive') ?></label>
                    <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Position to show message on product page', 'wdr-omnibus-directive'); ?></span>
                </td>
                <td>
                    <select name="position_to_show_message">
                        <option value="woocommerce_before_single_product_summary" <?php  ?>><?php _e('woocommerce_before_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_single_product_summary" <?php  ?>><?php _e('woocommerce_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_before_add_to_cart_form" <?php  ?>><?php _e('woocommerce_before_add_to_cart_form', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_before_add_to_cart_button" <?php  ?>><?php _e('woocommerce_before_add_to_cart_button', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_single_variation" <?php  ?>><?php _e('woocommerce_single_variation', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_before_add_to_cart_quantity" <?php  ?>><?php _e('woocommerce_before_add_to_cart_quantity', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_after_add_to_cart_quantity" <?php  ?>><?php _e('woocommerce_after_add_to_cart_quantity', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_after_add_to_cart_button" <?php  ?>><?php _e('woocommerce_after_add_to_cart_button', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_product_meta_start" <?php  ?>><?php _e('woocommerce_product_meta_start', 'wdr-omnibus-directive'); ?></option>
                        <option value="woocommerce_after_single_product_summary" <?php  ?>><?php _e('woocommerce_after_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <input class="button button-primary" type="submit" name="submit" value="Submit">
    </form>
</div>
