<?php
declare (strict_types=1);

namespace eduline\payment\gateways\wxpay;

use app\admin\logic\system\Config as SystemConfig;
use app\common\logic\Attach;
use eduline\admin\libs\pageform\FormItem;
use eduline\admin\page\PageForm;
use eduline\payment\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    protected static $key = 'system.package.payment.wxpay';

    public static function page(): PageForm
    {
        $fields = [
            'appid'       => FormItem::make()->title('APP的应用ID'),
            'app_id'      => FormItem::make()->title('公众号应用ID'),
            'miniapp_id'  => FormItem::make()->title('小程序应用ID'),
            'mch_id'      => FormItem::make()->title('商户号'),
            'key'         => FormItem::make()->title('API密钥'),
            'cert_client' => FormItem::make('upload')->accept('.pem')->autoupload()->limit(1)->title('API证书-client')->help('1. 证书格式为.pem，用于商户转账等场景使用<br />2. 可在【微信商户平台->账户中心->API安全】 中设置'),
            'cert_key'    => FormItem::make('upload')->accept('.pem')->autoupload()->limit(1)->title('API证书-key')->help('1. 证书格式为.pem，用于商户转账等场景使用<br />2. 可在【微信商户平台->账户中心->API安全】 中设置'),
        ];

        $form          = new PageForm();
        $form->pageKey = $fields;
        $form->withSystemConfig();
        $config                = self::get();
        $config['__key']       = self::$key;
        $config['cert_client'] = $config['cert_client'] ?? 0;
        $config['cert_key']    = $config['cert_key'] ?? 0;

        if ($config['cert_client']) {
            $config['cert_client_list'] = [
                [
                    'name' => app(Attach::class)->where('id', $config['cert_client'])->value('filename'),
                ],
            ];
        }

        if ($config['cert_key']) {
            $config['cert_key_list'] = [
                [
                    'name' => app(Attach::class)->where('id', $config['cert_key'])->value('filename'),
                ],
            ];
        }

        $form->datas = $config;

        return $form;
    }

    /**
     * 获取配置
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-28
     *
     * @return   [type]                         [description]
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
