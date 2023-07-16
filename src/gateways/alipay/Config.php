<?php
declare (strict_types=1);

namespace eduline\payment\gateways\alipay;

use app\admin\logic\system\Config as SystemConfig;
use app\common\logic\Attach;
use eduline\admin\libs\pageform\FormItem;
use eduline\admin\page\PageForm;
use eduline\payment\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    protected static $key = 'system.package.payment.alipay';

    public static function page(): PageForm
    {
        $fields = [
            'app_id'                 => FormItem::make()->title('应用ID')->required(),
            'private_key'            => FormItem::make('textarea')->title('应用私钥')->rows(20)->required(),
            // 'ali_public_key' => FormItem::make('textarea')->title('支付宝公钥')->rows(6)->required(),
            'app_cert_public_key'    => FormItem::make('upload')->title('应用公钥证书')->accept('.crt')->autoupload()->limit(1)->help('<a href="https://opendocs.alipay.com/open/291/105971#%E5%85%AC%E9%92%A5%E8%AF%81%E4%B9%A6%E6%96%B9%E5%BC%8F" target="_blank" class="el-link el-link--primary">查看支付宝文档</a>'),
            'alipay_cert_public_key' => FormItem::make('upload')->accept('.crt')->autoupload()->limit(1)->title('支付宝公钥证书'),
            'alipay_root_cert'       => FormItem::make('upload')->accept('.crt')->autoupload()->limit(1)->title('支付宝根证书'),
        ];

        $form          = new PageForm();
        $form->pageKey = $fields;
        $form->withSystemConfig();
        $config          = self::get();
        $config['__key'] = self::$key;

        $config['app_cert_public_key']    = $config['app_cert_public_key'] ?? 0;
        $config['alipay_cert_public_key'] = $config['alipay_cert_public_key'] ?? 0;
        $config['alipay_root_cert']       = $config['alipay_root_cert'] ?? 0;

        if ($config['app_cert_public_key']) {
            $config['app_cert_public_key_list'] = [
                [
                    'name' => app(Attach::class)->where('id', $config['app_cert_public_key'])->value('filename'),
                ],
            ];
        }

        if ($config['alipay_cert_public_key']) {
            $config['alipay_cert_public_key_list'] = [
                [
                    'name' => app(Attach::class)->where('id', $config['alipay_cert_public_key'])->value('filename'),
                ],
            ];
        }

        if ($config['alipay_root_cert']) {
            $config['alipay_root_cert_list'] = [
                [
                    'name' => app(Attach::class)->where('id', $config['alipay_root_cert'])->value('filename'),
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
        $config = SystemConfig::get(self::$key, [], request()->mhm_id);

        if ($name) {
            return isset($config[$name]) ? $config[$name] : null;
        }

        return $config;
    }

    /**
     * 获取证书路径
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-06-28
     *
     * @param int $id [description]
     * @return   [type]                             [description]
     */
    public static function getCrtPath($id)
    {
        if ($id) {
            $file = app(Attach::class)->where('id', $id)->findOrEmpty();
            if (!$file->isEmpty()) {
                return $file->filepath;
            }
        }

        return '';
    }
}
