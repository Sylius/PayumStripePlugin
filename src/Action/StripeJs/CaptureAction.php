<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Action\StripeJs;

use ArrayObject;
use FluxSE\PayumStripe\Action\StripeJs\CaptureAction as BaseCaptureAction;
use Payum\Core\Request\Generic;
use Stripe\PaymentIntent;

final class CaptureAction extends BaseCaptureAction
{
    /**
     * When an existing PaymentIntent is reused (the model already has an `id`), the base action only syncs it
     * and lets Payum redirect to the capture token after URL. If the buyer reloads the payment page before paying,
     * the PaymentIntent is still awaiting payment, so that redirect leaves them stuck on the order summary with
     * no way to complete the payment.
     *
     * Re-render the Stripe Elements form whenever the reused PaymentIntent is still in a payable state, so the buyer
     * can pay the very same intent (no duplicate intent is created).
     */
    protected function processNotNew(ArrayObject $model, Generic $request): void
    {
        parent::processNotNew($model, $request);

        /** @var string|null $status */
        $status = $model->offsetGet('status');
        if (!in_array($status, [
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
            PaymentIntent::STATUS_REQUIRES_CONFIRMATION,
            PaymentIntent::STATUS_REQUIRES_ACTION,
        ], true)) {
            return;
        }

        $paymentIntent = PaymentIntent::constructFrom($model->getArrayCopy());
        $this->render($paymentIntent, $request);
    }
}
