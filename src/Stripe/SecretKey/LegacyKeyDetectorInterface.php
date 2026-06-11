<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey;

interface LegacyKeyDetectorInterface
{
    public function isLegacy(?string $secretKey): bool;
}
