# Flexidengi payment gateway for [payum](http://payum.org/)

## Instalation (with symfony2 payum bundle)
add to your composer json
```json
{
    "require": {
        "payum/payum-bundle": "0.14.*",
        "fullpipe/payum-flexidengi": "dev-master"
    }
}
```

Add FlexidengiPaymentFactory to payum:
```php
<?php

// src/Acme/PaymentBundle/AcmePaymentBundle.php

namespace Acme\PaymentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Fullpipe\Payum\Flexidengi\Bridge\Symfony\FlexidengiPaymentFactory;

class AcmePaymentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('payum');
        $extension->addPaymentFactory(new FlexidengiPaymentFactory());
    }
}
```
