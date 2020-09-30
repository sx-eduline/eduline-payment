<?php


namespace eduline\payment\gateways\applepay;


use GuzzleHttp\Client;
use eduline\payment\exception\PayGatewayVerifyError;

class ApplePayService
{
    /**
     * 测试验证地址
     * @var string
     */
    private $sandboxUrl = 'https://sandbox.itunes.apple.com/verifyReceipt';
    /**
     * 正式验证地址
     * @var string
     */
    private $url = 'https://buy.itunes.apple.com/verifyReceipt';
    /**
     * 发送的数据
     * @var
     */
    private $data;

    /**
     * 验证
     * Author: Martinsun <syh@sunyonghong.com>
     * Date: 2020/9/29
     * @param $receipt_data
     * @return string
     * @throws PayGatewayVerifyError
     */
    public function verify($receipt_data)
    {
        $this->data = $receipt_data;
        $response   = $this->request($this->getRequestUrl());
        /**
         * 服务器二次验证代码
         * 21000 App Store不能读取你提供的JSON对象
         * 21002 receipt-data域的数据有问题
         * 21003 receipt无法通过验证
         * 21004 提供的shared secret不匹配你账号中的shared secret
         * 21005 receipt服务器当前不可用
         * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
         * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
         * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
         */
        if ($response->status == 21007) {
            $response = $this->request($this->getRequestUrl(true));
        } else if ($response->status == 21008) {
            $response = $this->request($this->getRequestUrl());
        }
        // 是否成功
        if (intval($response->status) === 0) {
            return $response;
        }

        throw new PayGatewayVerifyError('错误码: ' . $response->status, '支付失败');
    }

    /**
     * 发送请求
     * Author: Martinsun <syh@sunyonghong.com>
     * Date: 2020/9/29
     * @param $url
     * @return string
     */
    private function request($url)
    {
        $client = new Client();
        $result = $client->post($url, [
            'form_params' => [
                'receipt-data' => $this->data
            ]
        ]);

        $result = $result->getBody()->getContents();
        $result = json_decode($result);

        return $result;

    }

    /**
     * 获取验证请求地址
     * Author: Martinsun <syh@sunyonghong.com>
     * Date: 2020/9/29
     * @param bool $isSandbox
     * @return string
     */
    private function getRequestUrl(bool $isSandbox = false)
    {
        return $isSandbox ? $this->sandboxUrl : $this->url;
    }
}