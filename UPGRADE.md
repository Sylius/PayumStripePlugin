# UPGRADE FROM `v2.0.19` to `v2.1.0`

The minimum supported Sylius version has been raised: this plugin now requires `sylius/sylius: ^1.12`.

Sylius `1.9`, `1.10` and `1.11` are no longer supported. Upgrade your application to Sylius `~1.12.0`, `~1.13.0`
or `~1.14.0` before updating this plugin.

# UPGRADE FROM `v2.0.17` to `v2.0.18`

## `secret_key` set to a standard Stripe secret key (`sk_*`) is deprecated

Pasting a standard Stripe secret key (`sk_test_…` / `sk_live_…`) into the `Restricted API key (recommended) or secret key`
field of the Stripe gateway configuration is deprecated and will be removed in the next minor release. From then on, only
Restricted API Keys (`rk_test_…` / `rk_live_…`) will be accepted.

**Migration:**

1. Install the [Sylius Stripe App][link-sylius-stripe-app] on your Stripe account.
2. Open the App's Settings Page and copy the generated Restricted API Key (`rk_test_…` / `rk_live_…`).
3. In Sylius admin, edit your Stripe payment method and paste the `rk_*` key into the `Restricted API key (recommended) or secret key`
   field, replacing the previous `sk_*` value. Save.

The App ships with the minimum scopes the plugin needs, which is also why it becomes the only supported source for the
`secret_key` field from the next minor release onwards.

For Stripe's own rationale on why restricted keys exist and how they differ from standard secret keys,
see [Stripe's documentation on restricted API keys][link-stripe-restricted-keys].

[link-sylius-stripe-app]: https://marketplace.stripe.com/apps/install/link/com.sylius.stripe
[link-stripe-restricted-keys]: https://docs.stripe.com/keys/restricted-api-keys

## Gateway configuration admin form redesign

This form theme template has been removed :

- `@FluxSESyliusPayumStripePlugin/Admin/PaymentMethod/Form/useAuthorize.html.twig`
  use `@FluxSESyliusPayumStripePlugin/Admin/PaymentMethod/Form/gatewayConfiguration.html.twig` instead.

The Stripe gateway configuration admin form now renders information boxes next to the fields
(recommended Restricted API key, use authorize, webhook secret keys). If you overrode
`useAuthorize.html.twig` to customize the "use authorize" message, move your customization to
the new template. The "use authorize" info box is now always visible (previously it was shown
only when the checkbox was checked).

`StripeGatewayConfigurationType` now adds extra unmapped fields (`publishable_key_info`,
`secret_key_info`, `use_authorize_info`, `webhook_secret_keys_info`). They carry no data and
exist only to lay out the information boxes above, the persisted gateway configuration is unchanged.

# UPGRADE FROM `v2.0.10` to `v2.0.11`

This class has been deprecated :

- `\FluxSE\SyliusPayumStripePlugin\Form\Type\StripeCheckoutSessionGatewayConfigurationType`
  use `\FluxSE\SyliusPayumStripePlugin\Form\Type\StripeGatewayConfigurationType` instead.

# UPGRADE FROM `v2.0.8` to `v2.0.9`

This class has been renamed :

- `\FluxSE\SyliusPayumStripePlugin\StateMachine\CompleteAuthorizedOrderProcessor`
  to `\FluxSE\SyliusPayumStripePlugin\StateMachine\CaptureAuthorizedOrderProcessor`

This service has been renamed :

- `flux_se.sylius_payum_stripe.state_machine.complete_authorized`
  to `flux_se.sylius_payum_stripe.state_machine.capture_authorized`

# UPGRADE FROM `v2.0.7` to `v2.0.8`

This class has been renamed :

- `\FluxSE\SyliusPayumStripePlugin\StateMachine\CancelAuthorizedOrderProcessor`
  to `\FluxSE\SyliusPayumStripePlugin\StateMachine\CancelOrderProcessor`

This service has been renamed :

- `flux_se.sylius_payum_stripe.state_machine.cancel_authorized`
  to `flux_se.sylius_payum_stripe.state_machine.cancel`

# UPGRADE FROM `v1.2` TO `v2.0.0`

You will have to create or edit the configuration file :

```yaml
# config/packages/flux_se_sylius_payum_stripe.yaml

# add this imported file
imports:
    - { resource: "@FluxSESyliusPayumStripePlugin/Resources/config/config.yaml" }

flux_se_sylius_payum_stripe:
#  refund_disabled: true # set to false to enable refund
# ... keep the existing config
```

# UPGRADE FROM `v1.1.2` TO `v1.2.0`

* **BC BREAK**: This Sylius plugin has been renamed from
 `SyliusPayumStripeCheckoutSessionPlugin` to `SyliusPayumStripePlugin`
 to handle more than one gateway from Stripe.
* **BC BREAK**: Rename the namespace (vendor and plugin name) from 
 `Prometee\SyliusPayumStripeCheckoutSessionPlugin` to `FluxSE\SyliusPayumStripePlugin`
* **BC BREAK**: Rename the config root name from 
 `prometee_sylius_payum_stripe_session_checkout` to `flux_se_sylius_payum_stripe
* **BC BREAK**: Rename the parameters from 
 `prometee_sylius_payum_stripe_checkout_session.*` to `flux_se_sylius_payum_stripe.*`
* **BC BREAK**: Rename the service names from 
 `prometee_sylius_payum_stripe_checkout_session.*` to `flux_se.sylius_payum_stripe.*` 
 `prometee.sylius_payum_stripe_checkout_session.*` to `flux_se.sylius_payum_stripe.*`
* **BC BREAK**: Rename translation root name from
 `prometee_stripe_checkout_session_plugin` to `flux_se_sylius_payum_stripe`
