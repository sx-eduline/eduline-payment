<?php
declare (strict_types=1);

namespace eduline\payment\gateways\applepay;

use app\admin\logic\system\Config as SystemConfig;
use eduline\payment\exception\PayGatewayNotSupport;
use eduline\payment\interfaces\PayInterface;

/**
 * 苹果支付 - 支付类
 * @method object|string|void verify($receipt_data)
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
