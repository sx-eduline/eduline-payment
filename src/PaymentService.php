<?php

namespace eduline\payment;

use think\facade\Route;
use think\Service;

class PaymentService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function () {
            /** 接口路由 */
            Route::group('admin/system/package/payment', function () {
                // 支付列表
                Route::get('/list', 'index');
                // 配置页面
                Route::get('/<gateway>/config', 'config')->pattern(['gateway' => '[a-zA-Z_]+']);
                // 启用配置
                Route::post('/<gateway>/status', 'changeStatus')->pattern(['gateway' => '[a-zA-Z_]+']);
            })->prefix('\eduline\payment\admin\service\Config@')->middleware(['adminRoute', 'init', 'bindLoginUser']);
        });
    }
}
