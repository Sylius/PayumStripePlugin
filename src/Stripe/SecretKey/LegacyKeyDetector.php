<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey;

final class LegacyKeyDetector implements LegacyKeyDetectorInterface
{
    public function isLegacy(?string $secretKey): bool
    {
        return $secretKey !== null && strpos($secretKey, 'sk_') === 0;
    }
}
