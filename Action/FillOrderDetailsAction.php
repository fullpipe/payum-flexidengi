<?php

namespace Fullpipe\Payum\Flexidengi\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\FillOrderDetails;
use Payum\Core\Bridge\Spl\ArrayObject;
use Fullpipe\Payum\Flexidengi\Api;

class FillOrderDetailsAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param FillOrderDetails $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $order = $request->getOrder();
        $details = ArrayObject::ensureArrayObject($order->getDetails());

        if ($this->api->isSandbox()) {
            $details['payment_method_id'] = Api::PAYMENT_METHOD_TEST;
        }

        $details['order_id'] = $order->getNumber();
        $details['customer_id'] = $order->getClientEmail();

        if ($order->getTotalAmount()) {
            $details['summ'] = ((float) $order->getTotalAmount())/100;
            $details['currency'] = $order->getCurrencyCode();
        }

        $details->validateNotEmpty('order_id', 'customer_id');

        $order->setDetails($details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof FillOrderDetails;
    }
}
