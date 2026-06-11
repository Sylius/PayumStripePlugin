<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey;

use Sylius\Component\Core\Model\PaymentMethodInterface;

interface LegacyStripePaymentMethodsProviderInterface
{
    /** @return list<PaymentMethodInterface> */
    public function provide(): array;
}
