<?php

namespace Fullpipe\Payum\Flexidengi\Action;

use Fullpipe\Payum\Flexidengi\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $model['hash']) {
            $request->markNew();

            return;
        }

        if ($model['hash'] && null === $model['processing_status']) {
            $request->markPending();

            return;
        }

        switch ($model['processing_status']) {
            case Api::PAYMENT_STATUS_PROCESSED:
                $request->markCaptured();
                break;
            case Api::PAYMENT_STATUS_FAILED:
                $request->markFailed();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
