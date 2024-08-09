<?php
declare (strict_types=1);

namespace eduline\payment\gateways\applepay;

use app\admin\logic\system\Config as SystemConfig;
use app\common\logic\Attach;
use eduline\payment\exception\PayGatewayNotSupport;
use eduline\payment\interfaces\PayInterface;
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

    public function __construct(array $config)
    {
        // 检测是否开启支付宝支付
        $payment = SystemConfig::get('system.package.payment', [], request()->mhm_id);
        if (!in_array('applepay', $payment)) {
            throw new PayGatewayNotSupport("暂不支持该支付方式");
        }
    }

    /**
     * init
     * Author: Martinsun <syh@sunyonghong.com>
     * Date: 2020/9/29
     *
     * @return ApplePayService
     */
    public function init()
    {
        return new ApplePayService();
    }

}
