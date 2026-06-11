<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Twig\Extension;

use FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey\LegacyStripePaymentMethodsProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LegacyStripeKeyExtension extends AbstractExtension
{
    /** @var LegacyStripePaymentMethodsProviderInterface */
    private $legacyStripePaymentMethodsProvider;

    public function __construct(LegacyStripePaymentMethodsProviderInterface $legacyStripePaymentMethodsProvider)
    {
        $this->legacyStripePaymentMethodsProvider = $legacyStripePaymentMethodsProvider;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'flux_se_sylius_payum_stripe_legacy_payment_methods',
                [$this->legacyStripePaymentMethodsProvider, 'provide']
            ),
        ];
    }
}
