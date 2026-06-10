<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    private int $webhookSecretKeysListIndex = 0;

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'secret_key' => '#sylius_payment_method_gatewayConfig_config_secret_key',
            'webhook_secret_keys_list_element' => '#sylius_payment_method_gatewayConfig_config_webhook_secret_keys_%index%',
            'use_authorize_info' => '#sylius_payment_method_gatewayConfig_config_use_authorize_info',
            'use_authorize' => '#sylius_payment_method_gatewayConfig_config_use_authorize',
            'use_authorize_label' => 'label[for="sylius_payment_method_gatewayConfig_config_use_authorize"]',
        ]);
    }

    public function setStripeSecretKey(string $secretKey): void
    {
        $this->getElement('secret_key')->setValue($secretKey);
    }

    public function setStripePublishableKey(string $publishableKey): void
    {
        $this->getDocument()->fillField('Publishable key', $publishableKey);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setStripeWebhookSecretKey(string $webhookSecretKey): void
    {
        $this->getDocument()->clickLink('Add');
        $this
            ->getElement('webhook_secret_keys_list_element', [
                '%index%' => $this->webhookSecretKeysListIndex,
            ])
            ->setValue($webhookSecretKey)
        ;
        ++$this->webhookSecretKeysListIndex;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setStripeIsAuthorized(bool $isAuthorized): void
    {
        $checkbox = $this->getElement('use_authorize');
        if ($checkbox->isChecked() !== $isAuthorized) {
            // The checkbox input is visually hidden (Semantic UI), so clicking it directly is
            // intercepted by its label; click the label instead to toggle the value.
            $this->getElement('use_authorize_label')->click();
        }

        sleep(1);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function isUseAuthorizeWarningMessageDisplayed(): bool
    {
        return $this->getElement('use_authorize_info')->isVisible();
    }
}
