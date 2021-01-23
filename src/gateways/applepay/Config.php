<?php
declare (strict_types=1);

namespace eduline\payment\gateways\applepay;

use app\admin\logic\system\all;
use app\admin\logic\system\Config as SystemConfig;
use app\common\logic\Attach;
use eduline\admin\libs\pageform\FormItem;
use eduline\admin\page\PageForm;
use eduline\payment\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    protected static $key = 'system.package.payment.applepay';

    public static function page(): PageForm
    {
        $form = new PageForm();
        $form->withSystemConfig();

        return $form;
    }

    /**
     * 获取配置
     * Author: Martinsun <syh@sunyonghong.com>
     * Date: 2020/9/29
     *
     * @static
     * @param null $name
     * @return all|array|mixed|null
     */
    public static function get($name = null)
    {
        $config = SystemConfig::get(self::$key, []);

        if ($name) {
            return isset($config[$name]) ? $config[$name] : null;
        }

        return $config;
    }
}
