<?php

namespace Fullpipe\Payum\Flexidengi;

use Fullpipe\Payum\Flexidengi\Action\CaptureAction;
use Fullpipe\Payum\Flexidengi\Action\NotifyAction;
use Fullpipe\Payum\Flexidengi\Action\StatusAction;
use Fullpipe\Payum\Flexidengi\Action\FillOrderDetailsAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\PaymentFactory as CorePaymentFactory;
use Payum\Core\PaymentFactoryInterface;

class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $corePaymentFactory;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param array                   $defaultConfig
     * @param PaymentFactoryInterface $corePaymentFactory
     */
    public function __construct(array $defaultConfig = array(), PaymentFactoryInterface $corePaymentFactory = null)
    {
        $this->corePaymentFactory = $corePaymentFactory ?: new CorePaymentFactory();
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config = array())
    {
        return $this->corePaymentFactory->create($this->createConfig($config));
    }

    /**
     * {@inheritDoc}
     */
    public function createConfig(array $config = array())
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->corePaymentFactory->createConfig());

        $config->defaults(array(
            'payum.factory_name' => 'flexidengi',
            'payum.factory_title' => 'Flexidengi',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.fill_order_details' => new FillOrderDetailsAction(),
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'service_id' => null,
                'product_id' => null,
                'payment_method_id' => null,
                'secret' => null,
                'sandbox' => true,
            );

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array('service_id', 'secret');

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $flexidengiConfig = array(
                    'service_id' => $config['service_id'],
                    'product_id' => $config['product_id'],
                    'payment_method_id' => $config['payment_method_id'],
                    'secret' => $config['secret'],
                    'sandbox' => $config['sandbox'],
                );

                return new Api($flexidengiConfig);
            };
        }

        return (array) $config;
    }
}
