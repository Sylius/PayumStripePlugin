<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class StripeGatewayConfigurationType extends AbstractType
{
    public const PUBLISHABLE_KEY_PATTERN = '/^pk_(test|live)_/';

    public const SECRET_KEY_PATTERN = '/^rk_(test|live)_/';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishable_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.publishable_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.publishable_key',
                        'groups' => 'sylius',
                    ]),
                    new Regex([
                        'pattern' => self::PUBLISHABLE_KEY_PATTERN,
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.publishable_key.invalid_format',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('publishable_key_info', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.secret_key',
                        'groups' => 'sylius',
                    ]),
                    new Regex([
                        'pattern' => self::SECRET_KEY_PATTERN,
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.secret_key.invalid_format',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('secret_key_info', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('use_authorize', CheckboxType::class, [
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.use_authorize',
            ])
            ->add('use_authorize_info', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('webhook_secret_keys', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => 'flux_se_sylius_payum_stripe_plugin.form.gateway_configuration.stripe.webhook_secret_keys',
                'constraints' => [
                    new NotBlank([
                        'message' => 'flux_se_sylius_payum_stripe_plugin.stripe.webhook_secret_keys.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('webhook_secret_keys_info', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;
    }
}
