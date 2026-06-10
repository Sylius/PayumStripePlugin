<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Action\StripeJs;

use FluxSE\PayumStripe\Action\StripeJs\CaptureAction as BaseCaptureAction;
use FluxSE\PayumStripe\Request\CaptureAuthorized;
use FluxSE\PayumStripe\Request\StripeJs\Api\RenderStripeJs;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Sync;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;

final class CaptureActionSpec extends ObjectBehavior
{
    /**
     * @param Collaborator|GatewayInterface $gateway
     */
    public function let(GatewayInterface $gateway): void
    {
        $this->setGateway($gateway);
    }

    public function it_is_a_stripe_js_capture_action(): void
    {
        $this->shouldHaveType(BaseCaptureAction::class);
    }

    /**
     * @param Capture|Collaborator $request
     * @param Collaborator|TokenInterface $token
     * @param Collaborator|GatewayInterface $gateway
     */
    public function it_renders_the_form_again_when_the_reused_payment_intent_is_still_payable(
        Capture $request,
        TokenInterface $token,
        GatewayInterface $gateway
    ): void {
        $model = new ArrayObject([
            'id' => 'pi_123',
            'object' => 'payment_intent',
            'status' => 'requires_payment_method',
        ]);

        $request->getModel()->willReturn($model);
        $request->getToken()->willReturn($token);
        $token->getAfterUrl()->willReturn('https://example.com/after');

        $gateway->execute(Argument::type(Sync::class))->shouldBeCalled();
        $gateway->execute(Argument::type(CaptureAuthorized::class))->shouldBeCalled();
        $gateway->execute(Argument::type(RenderStripeJs::class))->shouldBeCalled();

        $this->execute($request);
    }

    /**
     * @param Capture|Collaborator $request
     * @param Collaborator|TokenInterface $token
     * @param Collaborator|GatewayInterface $gateway
     */
    public function it_does_not_render_the_form_when_the_reused_payment_intent_is_no_longer_payable(
        Capture $request,
        TokenInterface $token,
        GatewayInterface $gateway
    ): void {
        $model = new ArrayObject([
            'id' => 'pi_123',
            'object' => 'payment_intent',
            'status' => 'succeeded',
        ]);

        $request->getModel()->willReturn($model);
        $request->getToken()->willReturn($token);

        $gateway->execute(Argument::type(Sync::class))->shouldBeCalled();
        $gateway->execute(Argument::type(CaptureAuthorized::class))->shouldBeCalled();
        $gateway->execute(Argument::type(RenderStripeJs::class))->shouldNotBeCalled();

        $this->execute($request);
    }

    /**
     * @param Capture|Collaborator $request
     */
    public function it_supports_a_capture_request_with_an_array_access_model(
        Capture $request
    ): void {
        $request->getModel()->willReturn(new ArrayObject([]));

        $this->supports($request)->shouldReturn(true);
    }
}
