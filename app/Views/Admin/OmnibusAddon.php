<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    #wdr-od-configuration-form table tbody tr > td:first-child {
        width: 400px;
    }
</style>
<br>

<div id="wpbody-content" class="awdr-container">
    <div class="awdr-configuration-form">
        <form id="wdr-od-configuration-form" method="post">
            <h1><?php esc_attr_e('Omnibus Directive : General settings', 'wdr-omnibus-directive') ?></h1>
            <table class="wdr-general-setting form-table">
                <tbody style="background-color: #fff;">
                <tr>
                    <td>
                        <label class="awdr-left-align"><?php esc_attr_e('Select rules', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Select the product adjustment rules', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Note : Currently, we support product adjustment rules only', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <?php if(isset($check_enabled_rules) && is_array($check_enabled_rules)&& !empty($check_enabled_rules)) {
                        ?>
                        <select class="wdr-search-box awdr-od-select-rules" data-placeholder="<?php esc_attr_e(" Select rules here ", 'wdr-omnibus-directive');?>" name="wdr_od_selected_rules[]" multiple="true" >
                            <?php
                            foreach ($check_enabled_rules as $check_enabled_rule) {
                                echo '<option  ' . esc_html($check_enabled_rule['selected']) . '  value="' . esc_attr($check_enabled_rule['rule_id']) . '">' . esc_html($check_enabled_rule['rule_title']) . '</option>';
                            }
                            ?>
                        </select>
                        <?php
                        } else {
                            ?>
                            <div style="color:#ff6700"> <?php esc_attr_e("Currently we support only for the Discount type: Product Adjustment.", 'wdr-omnibus-directive') ?></div>
                            <div style="color:#ff6700"> <?php esc_attr_e("Seems you doesn't have any active Product adjustment rules.", 'wdr-omnibus-directive') ?></div>
                            <?php
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="awdr-left-align"><?php esc_attr_e('Number of days', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Record and display number of days after sale was started', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <input type="number" name="wdr_od_number_of_days" value="<?php echo !empty($number_of_days) ? esc_attr($number_of_days) : 30;?>" title="Number of days" size="4" min="30" max="" step="1" >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="awdr-left-align"><?php esc_attr_e('Show Omnibus message on product page', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Hide the message from omnibus plugin', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <input type="radio" data-name="hide_table_position" name="wdr_od_is_show_message_option"
                               id="is_show_omnibus_message_option_1" value="1"
                            <?php echo(!empty($is_show_omnibus_message_option) ? 'checked' : '')  ?>><label
                        for="is_show_omnibus_message_option_1"><?php esc_attr_e('Yes', 'wdr-omnibus-directive'); ?></label>

                        <input type="radio" data-name="hide_table_position" name="wdr_od_is_show_message_option"
                               id="is_show_omnibus_message_option" value="0"
                            <?php echo(empty($is_show_omnibus_message_option) ? 'checked' : '') ?>><label
                        for="is_show_omnibus_message_option"><?php esc_attr_e('No', 'wdr-omnibus-directive'); ?></label>
                    </td>
                </tr>

                <tr class="hide_table_position" id="wdr_od_omnibus_message" style="<?php echo empty($is_show_omnibus_message_option) ? 'display:none' : ''; ?>">
                    <td>
                        <label class="awdr-left-align"><?php esc_attr_e('Omnibus Message', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('You can use the following shortcode', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('{{price}} -> Replace the lowest price', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('{{date}} -> Display the day when was lowest price', 'wdr-omnibus-directive'); ?></span>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php _e('<strong>Eg</strong>:') . esc_html_e('Preview lowest price was {{price}} updated from {{date}}', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <?php $message = isset($message) && !empty($message)? $message : "Preview lowest price was {{price}} updated from {{date}}"; ?>
                        <textarea name="wdr_od_message" rows="5"  cols="30" > <?php esc_attr_e($message, 'wdr-omnibus-directive'); ?> </textarea>
                    </td>
                </tr>
                <tr class="hide_table_position" id="<?php echo empty($is_omnibus_plugin_active) ? 'wdr_od_override_omnibus_message_hide' : 'wdr_od_override_omnibus_message_show'; ?>" style="<?php echo empty($is_show_omnibus_message_option) || empty($is_omnibus_plugin_active) ? 'display:none' : ''; ?>" >
                    <td>
                    </td>
                    <td>
                        <?php $is_override_omnibus_message = isset($is_override_omnibus_message) ? $is_override_omnibus_message : 0; ?>
                        <input type="checkbox" name="wdr_od_is_override_omnibus_message" id="wdr_od_is_override_omnibus_message" value="1" <?php echo ( $is_override_omnibus_message == 1 ? 'checked' : '') ?>>
                        <label for="wdr_od_is_override_omnibus_message"><?php esc_attr_e('Override the message displayed by Omnibus plugin', 'wdr-omnibus-directive'); ?></label>
                    </td>
                </tr>
                <tr class="hide_table_position" id="wdr_od_select_message_position" style="<?php echo empty($is_show_omnibus_message_option) || !empty($is_override_omnibus_message) ? 'display:none' : ''; ?>" >
                    <td>
                        <label class="awdr-left-align"><?php esc_attr_e('Position to show message', 'wdr-omnibus-directive') ?></label>
                        <span class="wdr_settings_desc_text awdr-clear-both"><?php esc_attr_e('Position to show message on product page', 'wdr-omnibus-directive'); ?></span>
                    </td>
                    <td>
                        <select name="wdr_od_position_to_show_message">
                            <option value="woocommerce_get_price_html" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_get_price_html' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_get_price_html', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_single_product_summary' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_add_to_cart_form" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_add_to_cart_form' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_before_add_to_cart_form', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_product_meta_end" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_product_meta_end' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_product_meta_end', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_product_meta_start" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_product_meta_start' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_product_meta_start', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_add_to_cart_button" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_add_to_cart_button' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_before_add_to_cart_button', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_add_to_cart_quantity" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_add_to_cart_quantity' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_after_add_to_cart_quantity', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_add_to_cart_form" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_add_to_cart_form' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_after_add_to_cart_form', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_single_product" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_single_product' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_after_single_product', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_single_product" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_single_product' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_before_single_product', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_after_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_after_single_product_summary' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_after_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                            <option value="woocommerce_before_single_product_summary" <?php echo isset($position_to_show_message) && $position_to_show_message == 'woocommerce_before_single_product_summary' ? 'selected' : ''; ?>><?php esc_attr_e('woocommerce_before_single_product_summary', 'wdr-omnibus-directive'); ?></option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <?php wp_nonce_field('wdr_od_nonce_action', 'wdr_od_nonce_name'); ?>
            <input class="button button-primary" type="submit" name="wdr-od-submit" value="Submit">
        </form>
    </div>
</div>