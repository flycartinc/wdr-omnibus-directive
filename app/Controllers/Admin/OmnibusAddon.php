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
            'number_of_days' => $settings_data['number_of_days'],
            'is_show_omnibus_message_option' => $settings_data['is_show_omnibus_message_option'],
            'message' => $settings_data['message'],
            'is_override_omnibus_message' => $settings_data['is_override_omnibus_message'],
            'position_to_show_message' => $settings_data['position_to_show_message'],
            'is_omnibus_plugin_active' => $is_omnibus_plugin_active,
            'check_enabled_rules' => $check_enabled_rules,
        );
        self::$template_helper->setPath(WDR_OD_PLUGIN_PATH . 'app/Views/Admin/OmnibusAddon.php')->setData($params)->display();
    }
}