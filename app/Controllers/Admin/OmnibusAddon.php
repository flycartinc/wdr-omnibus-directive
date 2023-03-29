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
        $number_of_days = get_option('_awdr_price_lowest_days');
        $show_omnibus_message = get_option('_awdr_show_omnibus_message');
        $message = get_option('_awdr_om_message');
        $is_override_omnibus_message = get_option('_is_override_omnibus_message');
        $position_to_show_message = get_option('_awdr_position_to_show_message');
        $is_omnibus_plugin_active = self::$helper->isOmnibusPluginActive();

        $params = array(
            'section' => $section,
            'number_of_days' => $number_of_days,
            'show_omnibus_message' => $show_omnibus_message,
            'message' => $message,
            'is_override_omnibus_message' => $is_override_omnibus_message,
            'position_to_show_message' => $position_to_show_message,
            'is_omnibus_plugin_active' => $is_omnibus_plugin_active,
        );
        self::$template_helper->setPath(WDR_OD_PLUGIN_PATH . 'app/Views/Admin/OmnibusAddon.php')->setData($params)->display();
    }
}