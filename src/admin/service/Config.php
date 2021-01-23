<?php
declare (strict_types=1);

namespace eduline\payment\admin\service;

use app\admin\logic\system\Config as SystemConfig;
use app\common\service\BaseService;
use eduline\admin\libs\pagelist\ListItem;
use eduline\admin\page\PageList;
use eduline\payment\Gateways;
use think\facade\Request;

class Config extends BaseService
{
    /**
     * 支付网关列表
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-27
     *
     * @return   [type]                         [description]
     */
    public function index()
    {
        $gateways = Gateways::getGateways();
        $payment  = SystemConfig::get('system.package.payment');
        // 查询配置
        foreach ($gateways as $key => $gateway) {
            // 储存配置key
            $__key                    = 'system.package.payment.' . $gateway['key'];
            $gateways[$key]['__key']  = $__key;
            $gateways[$key]['config'] = SystemConfig::get($__key);
            $gateways[$key]['status'] = in_array($gateway['key'], $payment) ? 1 : 0;
        }
        // 定义字段
        $keyList = [
            'key'    => ListItem::make()->title('支付网关'),
            'name'   => ListItem::make()->title('网关名称'),
            'desc'   => ListItem::make()->title('描述'),
            'status' => ListItem::make('custom')->title('启用状态'),
        ];

        // 设置表单
        $list = app(PageList::class);
        // 表单字段
        $list->pageKey = $keyList;
        $list->datas   = $gateways;

        return $list->send();
    }

    /**
     * 支付配置
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-03-27
     *
     * @return   [type]                         [description]
     */
    public function config($gateway)
    {
        // 配置界面
        $form = Gateways::getGatewayConfigPage($gateway);

        return $form->send();
    }

    /**
     * 改变支付状态
     * Author   Martinsun<syh@sunyonghong.com>
     * Date:  2020-06-05
     *
     * @param    [type]                         $gateway [description]
     * @return   [type]                                  [description]
     */
    public function changeStatus($gateway)
    {
        $key     = 'system.package.payment';
        $payment = SystemConfig::get($key);
        $status  = Request::post('status/d', 0);

        if ($status == 1 && !in_array($gateway, $payment)) {
            $payment[] = $gateway;
        } else if ($status == 0 && in_array($gateway, $payment)) {
            $index = array_search($gateway, $payment);
            unset($payment[$index]);
        }

        $payment = array_values($payment);

        SystemConfig::set($key, $payment);

        return $this->parseToData([], 1, '保存成功');
    }
}
