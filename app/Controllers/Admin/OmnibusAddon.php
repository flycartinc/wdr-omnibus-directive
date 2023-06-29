<?php
namespace WDR_OD\App\Controllers\Admin;
use Wdr\App\Controllers\Admin\Addons\Base;
use WDR_OD\App\Helpers\Helper;
defined('ABSPATH') or exit;

class OmnibusAddon extends Base {

    private static $helper;

    protected $addon = 'omnibus_directive';

    /**
     * Add the plugin to Discount rules Add-on tab
     * @param $task
     * @return mixed|void
     */
    function render($task = null) {

        self::$helper = empty( self::$helper) ? new Helper() : self::$helper;

        $section = $this->input->get('section');
        $settings_data = get_option('wdr_omnibus_directive');
        $is_omnibus_plugin_active = self::$helper->isOmnibusPluginActive();
        $check_enabled_rules = self::$helper->checkRuleEnabled();

        $params = array(
            'section' => $section,
            'number_of_days' => isset($settings_data['number_of_days']) ? $settings_data['number_of_days'] : 30,
            'is_show_omnibus_message_option' => isset($settings_data['is_show_omnibus_message_option']) ? $settings_data['is_show_omnibus_message_option'] : 0,
            'message' => isset($settings_data['message']) ? wp_unslash($settings_data['message']) : "Preview lowest price was {{price}} updated on {{date}}",
            'is_override_omnibus_message' => isset($settings_data['is_override_omnibus_message']) ? $settings_data['is_override_omnibus_message'] : 0,
            'position_to_show_message' => isset($settings_data['position_to_show_message']) ? $settings_data['position_to_show_message'] : "woocommerce_get_price_html",
            'is_omnibus_plugin_active' => isset($is_omnibus_plugin_active) ? $is_omnibus_plugin_active : 0,
            'check_enabled_rules' => isset($check_enabled_rules) && !empty($check_enabled_rules) && is_array($check_enabled_rules) ? $check_enabled_rules : array(),
            'check_select_all_product_adjustment' => isset($settings_data['selected_rules']) && !empty($settings_data['selected_rules']) && is_array($settings_data['selected_rules']) ? $settings_data['selected_rules'] : array(),
        );
        self::$template_helper->setPath(WDR_OD_PLUGIN_PATH . 'app/Views/Admin/OmnibusAddon.php')->setData($params)->display();
    }
}