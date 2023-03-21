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

class OmnibusAddon extends Base {
    protected $addon = 'omnibus_directive';

    /**
     * Add the plugin to Discount rules Add-on tab
     * @param $task
     * @return mixed|void
     */
    function render($task = null)
    {
        $number_of_days = get_option('_awdr_price_lowest_days');
        $show_omnibus_message = get_option('_awdr_show_omnibus_message') ;

        $section = $this->input->get('section');
        $params = array(
            'section' => $section,
            'number_of_days' => $number_of_days,
            'show_omnibus_message' => $show_omnibus_message,
        );
        self::$template_helper->setPath(WDR_OD_PLUGIN_PATH . 'app/Views/Admin/OmnibusAddon.php')->setData($params)->display();
    }
}