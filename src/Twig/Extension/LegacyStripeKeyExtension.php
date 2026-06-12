<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Twig\Extension;

use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyKeyDetectorInterface;
use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyStripePaymentMethodsProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LegacyStripeKeyExtension extends AbstractExtension
{
    /** @var LegacyStripePaymentMethodsProviderInterface */
    private $legacyStripePaymentMethodsProvider;

    /** @var LegacyKeyDetectorInterface */
    private $legacyKeyDetector;

    public function __construct(
        LegacyStripePaymentMethodsProviderInterface $legacyStripePaymentMethodsProvider,
        LegacyKeyDetectorInterface $legacyKeyDetector
    ) {
        $this->legacyStripePaymentMethodsProvider = $legacyStripePaymentMethodsProvider;
        $this->legacyKeyDetector = $legacyKeyDetector;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'flux_se_sylius_payum_stripe_legacy_payment_methods',
                [$this->legacyStripePaymentMethodsProvider, 'provide']
            ),
            new TwigFunction(
                'flux_se_sylius_payum_stripe_is_legacy_secret_key',
                [$this->legacyKeyDetector, 'isLegacy']
            ),
        ];
    }
}
