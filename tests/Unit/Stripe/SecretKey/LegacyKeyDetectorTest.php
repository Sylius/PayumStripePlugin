<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Stripe\SecretKey;

use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyKeyDetector;
use PHPUnit\Framework\TestCase;

final class LegacyKeyDetectorTest extends TestCase
{
    public function testItFlagsStandardSecretKeysAsLegacy(): void
    {
        $detector = new LegacyKeyDetector();

        self::assertTrue($detector->isLegacy('sk_test_abc'));
        self::assertTrue($detector->isLegacy('sk_live_abc'));
    }

    public function testItDoesNotFlagRestrictedApiKeys(): void
    {
        self::assertFalse((new LegacyKeyDetector())->isLegacy('rk_test_abc'));
    }

    public function testItDoesNotFlagOtherPrefixes(): void
    {
        self::assertFalse((new LegacyKeyDetector())->isLegacy('pk_test_abc'));
    }

    public function testItDoesNotFlagNull(): void
    {
        self::assertFalse((new LegacyKeyDetector())->isLegacy(null));
    }
}
