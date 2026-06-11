<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Stripe\SecretKey;

use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyKeyDetector;
use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyStripePaymentMethodsProvider;
use Payum\Core\Model\GatewayConfigInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

final class LegacyStripePaymentMethodsProviderTest extends TestCase
{
    /** @var list<string> */
    private const STRIPE_FACTORY_NAMES = ['stripe_checkout_session', 'stripe_js'];

    public function testItReturnsNoMethodsWhenNoneExist(): void
    {
        self::assertSame([], $this->createProvider([])->provide());
    }

    public function testItIgnoresNonStripePaymentMethods(): void
    {
        $paymentMethod = $this->paymentMethodWithGatewayConfig('offline', ['secret_key' => 'sk_live_abc']);

        self::assertSame([], $this->createProvider([$paymentMethod])->provide());
    }

    public function testItSkipsPaymentMethodsWithoutGatewayConfig(): void
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $paymentMethod->method('getGatewayConfig')->willReturn(null);

        self::assertSame([], $this->createProvider([$paymentMethod])->provide());
    }

    public function testItSkipsStripePaymentMethodsUsingRestrictedKeys(): void
    {
        $paymentMethod = $this->paymentMethodWithGatewayConfig('stripe_checkout_session', ['secret_key' => 'rk_live_abc']);

        self::assertSame([], $this->createProvider([$paymentMethod])->provide());
    }

    public function testItSkipsStripePaymentMethodsWithoutSecretKey(): void
    {
        $paymentMethod = $this->paymentMethodWithGatewayConfig('stripe_js', []);

        self::assertSame([], $this->createProvider([$paymentMethod])->provide());
    }

    public function testItReturnsOneEntryPerStripePaymentMethodUsingLegacyKey(): void
    {
        $legacyOne = $this->paymentMethodWithGatewayConfig('stripe_checkout_session', ['secret_key' => 'sk_live_abc']);
        $modern = $this->paymentMethodWithGatewayConfig('stripe_js', ['secret_key' => 'rk_live_abc']);
        $legacyTwo = $this->paymentMethodWithGatewayConfig('stripe_js', ['secret_key' => 'sk_test_def']);

        self::assertSame(
            [$legacyOne, $legacyTwo],
            $this->createProvider([$legacyOne, $modern, $legacyTwo])->provide()
        );
    }

    /**
     * @param list<PaymentMethodInterface> $paymentMethods
     */
    private function createProvider(array $paymentMethods): LegacyStripePaymentMethodsProvider
    {
        $repository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $repository
            ->method('findBy')
            ->with(['enabled' => true])
            ->willReturn($paymentMethods)
        ;

        return new LegacyStripePaymentMethodsProvider($repository, new LegacyKeyDetector(), self::STRIPE_FACTORY_NAMES);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function paymentMethodWithGatewayConfig(string $factoryName, array $config): PaymentMethodInterface
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getFactoryName')->willReturn($factoryName);
        $gatewayConfig->method('getConfig')->willReturn($config);

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $paymentMethod->method('getGatewayConfig')->willReturn($gatewayConfig);

        return $paymentMethod;
    }
}
