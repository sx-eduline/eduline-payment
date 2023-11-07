<?php
declare (strict_types=1);

namespace eduline\payment\gateways\alipay;

use app\admin\logic\system\Config as SystemConfig;
use eduline\admin\page\PageForm;
use eduline\payment\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    protected static $key = 'system.package.payment.balance';

    public static function page(): PageForm
    {
        $fields = [];

        $form          = new PageForm();
        $form->pageKey = $fields;
        $form->withSystemConfig();
        $config          = self::get();
        $config['__key'] = self::$key;

        $form->datas = $config;

        return $form;
    }

    /**
     * 获取配置
     *
     * @return   [type]                         [description]
     */
    public static function get($name = null)
    {
        $config = SystemConfig::get(self::$key, [], request()->mhm_id);

        if ($name) {
            return isset($config[$name]) ? $config[$name] : null;
        }

        return $config;
    }
}
