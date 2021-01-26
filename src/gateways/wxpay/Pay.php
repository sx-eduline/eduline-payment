<?php
declare (strict_types=1);

namespace eduline\payment\gateways\wxpay;

use app\admin\logic\system\Config as SystemConfig;
use app\common\logic\Attach;
use eduline\payment\exception\PayGatewayNotSupport;
use eduline\payment\interfaces\PayInterface;
use think\facade\App;
use think\facade\Env;
use Yansongda\Pay\Pay as PayGateway;

/**
 * 微信支付 - 支付类
 * @method Response app() APP 支付
 * @method Collection groupRedpack() 分裂红包
 * @method Collection miniapp() 小程序支付
 * @method Collection mp() 公众号支付
 * @method Collection pos() 刷卡支付
 * @method Collection redpack() 普通红包
 * @method Collection scan() 扫码支付
 * @method Collection transfer() 企业付款
 * @method Response wap() H5 支付
 */
class Pay implements PayInterface
{
    private $config;

    public function __construct(array $config)
    {
        // 检测是否开启支付宝支付
        $payment = SystemConfig::get('system.package.payment');
        if (!in_array('wxpay', $payment)) {
            throw new PayGatewayNotSupport("暂不支持该支付方式");
        }

        $this->config = $this->getConfig($config);
    }

    public function getConfig(array $config)
    {

        return array_merge([
            'appid'       => Config::get('appid'), // APP APPID
            'app_id'      => Config::get('app_id'), // 公众号 APPID
            'miniapp_id'  => Config::get('miniapp_id'), // 小程序 APPID
            'mch_id'      => Config::get('mch_id'),
            'key'         => Config::get('key'),
            'cert_client' => app(Attach::class)->where('id', Config::get('cert_client'))->findOrEmpty()->getAttr('filePath'), // optional，退款等情况时用到
            'cert_key'    => app(Attach::class)->where('id', Config::get('cert_key'))->findOrEmpty()->getAttr('filePath'), // optional，退款等情况时用到
            'log'         => [ // optional
                'file'     => App::getRuntimePath() . 'paylogs' . DIRECTORY_SEPARATOR . 'wxpay.log',
                'level'    => Env::get('app_debug') ? 'debug' : 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type'     => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http'        => [ // optional
                'timeout'         => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
        ], $config);
    }

    public function init()
    {
        return PayGateway::wechat($this->config);
    }
}
