<?php
declare (strict_types=1);

namespace eduline\payment\gateways\alipay;

use app\admin\logic\system\Config as SystemConfig;
use eduline\payment\exception\PayGatewayNotSupport;
use eduline\payment\interfaces\PayInterface;
use think\facade\App;
use think\facade\Env;
use Yansongda\Pay\Pay as PayGateway;

/**
 * 支付宝-支付类
 *
 * 初始化完成之后,可以继续调用对应的方法进行支付
 * @method Response app() APP 支付
 * @method Collection pos() 刷卡支付
 * @method Collection scan() 扫码支付
 * @method Collection transfer() 帐户转账
 * @method Response wap() 手机网站支付
 * @method Response web() 电脑支付
 */
class Pay implements PayInterface
{
    private $config;

    public function __construct(array $config)
    {
        // 检测是否开启支付宝支付
        $payment = SystemConfig::get('system.package.payment', [], request()->mhm_id);
        if (!in_array('alipay', $payment)) {
            throw new PayGatewayNotSupport("暂不支持该支付方式");
        }

        $this->config = $this->getConfig($config);
    }

    public function getConfig(array $config)
    {
        $mhm = $config['mhm'] ?? null;
        return array_merge([
            'app_id'              => Config::get('app_id', $mhm),
            'private_key'         => Config::get('private_key', $mhm),
            'app_cert_public_key' => Config::getCrtPath(Config::get('app_cert_public_key', $mhm)),
            'ali_public_key'      => Config::getCrtPath(Config::get('alipay_cert_public_key', $mhm)),
            'alipay_root_cert'    => Config::getCrtPath(Config::get('alipay_root_cert', $mhm)),
            'log'                 => [ // optional
                'file'     => App::getRuntimePath() . 'paylogs' . DIRECTORY_SEPARATOR . 'alipay.log',
                'level'    => Env::get('app_debug') ? 'debug' : 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type'     => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http'                => [ // optional
                'timeout'         => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
        ], $config);
    }

    public function init()
    {
        return PayGateway::alipay($this->config);
    }
}
