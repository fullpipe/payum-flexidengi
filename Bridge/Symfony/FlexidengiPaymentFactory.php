<?php

namespace Fullpipe\Payum\Flexidengi\Bridge\Symfony;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class FlexidengiPaymentFactory extends AbstractPaymentFactory
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'flexidengi';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
            ->scalarNode('service_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('product_id')->defaultNull()->end()
            ->scalarNode('payment_method_id')->defaultNull()->end()
            ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
            ->booleanNode('sandbox')->defaultTrue()->end()
            ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumPaymentFactoryClass()
    {
        return 'Fullpipe\Payum\Flexidengi\PaymentFactory';
    }

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackage()
    {
        return 'fullpipe/payum-flexidengi';
    }
}
