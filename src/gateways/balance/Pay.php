<?php
declare (strict_types=1);

namespace eduline\payment\gateways\balance;

use app\admin\logic\system\Config as SystemConfig;
use eduline\payment\exception\PayGatewayNotSupport;
use eduline\payment\interfaces\PayInterface;

/**
 * 余额支付
 */
class Pay implements PayInterface
{

    public function __construct()
    {
        // 检测是否开启支付宝支付
        $payment = SystemConfig::get('system.package.payment', [], request()->mhm_id);
        if (!in_array('balance', $payment)) {
            throw new PayGatewayNotSupport("暂不支持该支付方式");
        }
    }

    /**
     * init
     *
     * @return $this
     */
    public function init()
    {
        return new static();
    }

}
