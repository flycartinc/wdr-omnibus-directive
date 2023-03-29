<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    #wdr-om-configuration-form table tbody tr > td:first-child {
        width: 400px;
    }
</style>
<br>

<div id="wpbody-content" class="awdr-container">
    <div class="awdr-configuration-form">
        <form id="wdr-om-configuration-form" method="post">
            <h1><?php _e('Omnibus Directive : General settings', 'wdr-omnibus-directive') ?></h1>
            <table class="wdr-general-setting form-table">
                <tbody style="background-color: #fff;">
                <tr>
                    <td>
                        <label class="awdr-left-align"><?php _e('Number of days', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Record and display number of days after sale was started', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <input type="number" name="awdr_refresh_date" value="<?php echo !empty($number_of_days) ? $number_of_days : 30;?>" title="Number of days" size="4" min="30" max="" step="1" >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="awdr-left-align"><?php _e('Show Omnibus message on product page', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Hide the message from omnibus plugin', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <input type="radio" data-name="hide_table_position" name="show_omnibus_message_option"
                               value="1"
                            <?php echo(!empty($show_omnibus_message) ? 'checked' : '')  ?>><label
                        ><?php _e('Yes', 'wdr-omnibus-directive'); ?></label>

                        <input type="radio" data-name="hide_table_position" name="show_omnibus_message_option"
                               value="0"
                            <?php echo(empty($show_omnibus_message) ? 'checked' : '') ?>><label
                        ><?php _e('No', 'wdr-omnibus-directive'); ?></label>
                    </td>
                </tr>

                <tr class="hide_table_position" id="wdr_om_omnibus_message" style="<?php echo empty($show_omnibus_message) ? 'display:none' : ''; ?>">
                    <td>
                        <label class="awdr-left-align"><?php _e('Omnibus Message', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('You can use the following shortcode', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('{{price}} -> Replace the lowest price', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('{{date}} -> Display the day when was lowest price', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php _e('<strong>Eg</strong>: Preview lowest price: {price}.', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <?php $message = isset($message) && !empty($message)? $message : "Preview lowest price was {{price}} updated from {{date}}"; ?>
                        <textarea name="awdr_om_message" rows="5"  cols="30" > <?php _e($message, 'wdr-omnibus-directive'); ?> </textarea>
                    </td>
                </tr>
                <tr class="hide_table_position" id="wdr_om_override_omnibus_message" style="<?php echo empty($show_omnibus_message) || empty($is_omnibus_plugin_active) ? 'display:none' : ''; ?>" >
                    <td>
                    </td>
                    <td>
                        <?php $is_override_omnibus_message = isset($is_override_omnibus_message) ? $is_override_omnibus_message : 0; ?>
                        <input type="checkbox" name="is_override_omnibus_message" value="1" <?php echo ( $is_override_omnibus_message == 1 ? 'checked' : '') ?>>
                        <label><?php _e('Override the message displayed by Omnibus plugin', 'wdr-omnibus-directive'); ?></label>
                    </td>
                </tr>
                <tr  class="hide_table_position" id="wdr_om_select_rule" style="<?php echo empty($show_omnibus_message) ? 'display:none' : ''; ?>" >
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
                <tr class="hide_table_position" id="wdr_om_select_message_position" style="<?php echo empty($show_omnibus_message) ? 'display:none' : ''; ?>" >
                    <td>
                        <label for="" class="awdr-left-align"><?php _e('Position to show message', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Position to show message on product page', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <select name="position_to_show_message">
                            <option value="woocommerce_before_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_single_product_summary' ? 'selected' : ''; ?>><?php _e('woocommerce_before_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_single_product_summary' ? 'selected' : ''; ?>><?php _e('woocommerce_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_add_to_cart_form" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_add_to_cart_form' ? 'selected' : ''; ?>><?php _e('woocommerce_before_add_to_cart_form', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_add_to_cart_button" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_add_to_cart_button' ? 'selected' : ''; ?>><?php _e('woocommerce_before_add_to_cart_button', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_single_variation" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_single_variation' ? 'selected' : ''; ?>><?php _e('woocommerce_single_variation', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_add_to_cart_quantity" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_add_to_cart_quantity' ? 'selected' : ''; ?>><?php _e('woocommerce_before_add_to_cart_quantity', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_add_to_cart_quantity" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_add_to_cart_quantity' ? 'selected' : ''; ?>><?php _e('woocommerce_after_add_to_cart_quantity', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_add_to_cart_button" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_add_to_cart_button' ? 'selected' : ''; ?>><?php _e('woocommerce_after_add_to_cart_button', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_product_meta_start" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_product_meta_start' ? 'selected' : ''; ?>><?php _e('woocommerce_product_meta_start', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_single_product_summary' ? 'selected' : ''; ?>><?php _e('woocommerce_after_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <input class="button button-primary" type="submit" name="submit" value="Submit">
        </form>
    </div>
</div>