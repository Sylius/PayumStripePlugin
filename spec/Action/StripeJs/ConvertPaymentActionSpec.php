<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Action\StripeJs;

use FluxSE\SyliusPayumStripePlugin\Action\StripeJs\ConvertPaymentAction;
use FluxSE\SyliusPayumStripePlugin\Action\StripeJs\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\StripeJs\DetailsProviderInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentActionSpec extends ObjectBehavior
{
    /**
     * @param Collaborator|DetailsProviderInterface $detailsProvider
     */
    public function let(DetailsProviderInterface $detailsProvider): void
    {
        $this->beConstructedWith($detailsProvider);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
        $this->shouldHaveType(ConvertPaymentActionInterface::class);
    }

    /**
     * @param Convert|Collaborator $request
     * @param Collaborator|PaymentInterface $payment
     * @param Collaborator|DetailsProviderInterface $detailsProvider
     */
    public function it_returns_existing_details_when_an_id_is_already_set(
        Convert $request,
        PaymentInterface $payment,
        DetailsProviderInterface $detailsProvider
    ): void {
        $details = ['id' => 'pi_123', 'object' => 'payment_intent'];
        $payment->getDetails()->willReturn($details);
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $detailsProvider->getDetails($payment)->shouldNotBeCalled();
        $request->setResult($details)->shouldBeCalled();

        $this->execute($request);
    }

    /**
     * @param Convert|Collaborator $request
     * @param Collaborator|PaymentInterface $payment
     * @param Collaborator|DetailsProviderInterface $detailsProvider
     */
    public function it_builds_fresh_details_when_no_id_is_set(
        Convert $request,
        PaymentInterface $payment,
        DetailsProviderInterface $detailsProvider
    ): void {
        $freshDetails = ['amount' => 1000, 'currency' => 'usd'];
        $payment->getDetails()->willReturn([]);
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');
        $detailsProvider->getDetails($payment)->willReturn($freshDetails);

        $request->setResult($freshDetails)->shouldBeCalled();

        $this->execute($request);
    }

    /**
     * @param Convert|Collaborator $request
     * @param Collaborator|PaymentInterface $payment
     */
    public function it_supports_only_convert_request_payment_source_and_array_to(
        Convert $request,
        PaymentInterface $payment
    ): void {
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $this->supports($request)->shouldReturn(true);
    }
}
