<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Stripe\SecretKey;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

final class LegacyStripePaymentMethodsProvider implements LegacyStripePaymentMethodsProviderInterface
{
    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var LegacyKeyDetectorInterface */
    private $legacyKeyDetector;

    /** @var list<string> */
    private $stripeFactoryNames;

    /** @param list<string> $stripeFactoryNames */
    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        LegacyKeyDetectorInterface $legacyKeyDetector,
        array $stripeFactoryNames
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->legacyKeyDetector = $legacyKeyDetector;
        $this->stripeFactoryNames = $stripeFactoryNames;
    }

    public function provide(): array
    {
        $legacyPaymentMethods = [];

        foreach ($this->paymentMethodRepository->findBy(['enabled' => true]) as $paymentMethod) {
            if (!$paymentMethod instanceof PaymentMethodInterface) {
                continue;
            }

            $gatewayConfig = $paymentMethod->getGatewayConfig();
            if ($gatewayConfig === null) {
                continue;
            }

            if (!in_array($gatewayConfig->getFactoryName(), $this->stripeFactoryNames, true)) {
                continue;
            }

            /** @var mixed $secretKey */
            $secretKey = $gatewayConfig->getConfig()['secret_key'] ?? null;
            if ($secretKey !== null && !is_string($secretKey)) {
                continue;
            }

            if (!$this->legacyKeyDetector->isLegacy($secretKey)) {
                continue;
            }

            $legacyPaymentMethods[] = $paymentMethod;
        }

        return $legacyPaymentMethods;
    }
}
