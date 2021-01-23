<?php
declare (strict_types=1);

namespace eduline\payment;

use eduline\payment\exception\PayGatewayNotFound;

class Pay
{
    public static function __callStatic($gateway, $config)
    {
        $stdclass = __NAMESPACE__ . '\\gateways\\' . $gateway . '\\Pay';

        if (class_exists($stdclass)) {
            $config = $config[0] ?? [];

            $class = new $stdclass($config);

            return $class->init();
        }

        throw new PayGatewayNotFound("不支持的网关");

    }
}
