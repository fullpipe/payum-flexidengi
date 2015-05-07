<?php

namespace Fullpipe\Payum\Flexidengi;

class Api
{
    const PAYMENT_METHOD_WEBMONEY_WMR = 38; // WebMoney WMR
    const PAYMENT_METHOD_QIWI = 44; // QIWI
    const PAYMENT_METHOD_TEST = 45; // Тестовый(возвращает произвольный статус оплаты, не передается в реестрах)
    const PAYMENT_METHOD_MOBILE = 60; // Мобильная коммерция
    const PAYMENT_METHOD_CREDIT_CARDS = 61; // Банковские карты VISA / VISA Electron / MasterCard / Maestro

    const PAYMENT_STATUS_PROCESSED = 'PROCESSED';
    const PAYMENT_STATUS_FAILED = 'FAILED';

    const ORDER_ID_PARAM_NAME = 'order_id';

    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getPaymentPageUrl()
    {
        return 'https://pay.flexidengi.ru/';
    }

    public function getServiceId()
    {
        return $this->config['service_id'];
    }

    public function getProductId()
    {
        return $this->config['product_id'];
    }

    private function getSecret()
    {
        return $this->config['secret'];
    }

    public function isSandbox()
    {
        return $this->config['sandbox'];
    }

    /**
     * Build order signature from order details.
     * md5(service_id+customer_id+order_id+product_id+ summ+currency+count+payment_method_id+ secret_key).
     *
     * @param array $params
     *
     * @return string
     */
    public function sing(array $params)
    {
        $singParams = array(
            'service_id' => $this->getServiceId(),
            'customer_id' => null,
            'order_id' => null,
            'product_id' => null,
            'summ' => null,
            'currency' => null,
            'count' => null,
            'payment_method_id' => null,
            'secret_key' => $this->getSecret(),
        );

        $params = array_intersect_key($params, $singParams);
        $singParams = array_merge($singParams, $params);

        return md5(implode('', $singParams));
    }

    /**
     * Validate notification signature.
     *
     * @param array $params
     *
     * @return boolean
     */
    public function validateNotificationSignature(array $params)
    {
        $hash = $params['hash'];
        $signatureParams = array(
            'service_id' => null,
            'transaction_id' => null,
            'customer_id' => null,
            'order_id' => null,
            'processing_status' => null,
            'price' => null,
            'price_rub' => null,
            'currency' => null,
            'share' => null,
            'share_rub' => null,
            'transaction_date' => null,
            'payment_method_id' => null,
            'product_id' => null,
            'secret_key' => $this->getSecret(),
        );

        $params = array_intersect_key($params, $signatureParams);
        $signatureParams = array_merge($signatureParams, $params);
        $signature = md5(implode('', $signatureParams));

        return $signature == $hash;
    }
}
