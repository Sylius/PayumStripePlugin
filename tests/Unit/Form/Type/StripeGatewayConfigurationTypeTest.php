<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Form\Type;

use FluxSE\SyliusPayumStripePlugin\Form\Type\StripeGatewayConfigurationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class StripeGatewayConfigurationTypeTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
    }

    /** @dataProvider acceptedSecretKeyProvider */
    public function testSecretKeyFieldAcceptsRestrictedKeys(string $key): void
    {
        self::assertCount(0, $this->validator->validate($key, $this->secretKeyConstraints()));
    }

    /** @dataProvider rejectedSecretKeyProvider */
    public function testSecretKeyFieldRejectsNonRestrictedKeys(string $key): void
    {
        self::assertGreaterThan(0, $this->validator->validate($key, $this->secretKeyConstraints())->count());
    }

    /** @dataProvider acceptedPublishableKeyProvider */
    public function testPublishableKeyFieldAcceptsPublishableKeys(string $key): void
    {
        self::assertCount(0, $this->validator->validate($key, $this->publishableKeyConstraints()));
    }

    /** @dataProvider rejectedPublishableKeyProvider */
    public function testPublishableKeyFieldRejectsNonPublishableKeys(string $key): void
    {
        self::assertGreaterThan(0, $this->validator->validate($key, $this->publishableKeyConstraints())->count());
    }

    /** @return iterable<string, array{string}> */
    public static function acceptedSecretKeyProvider(): iterable
    {
        yield 'restricted key in test mode' => ['rk_test_abc123'];
        yield 'restricted key in live mode' => ['rk_live_abc123'];
    }

    /** @return iterable<string, array{string}> */
    public static function rejectedSecretKeyProvider(): iterable
    {
        yield 'empty string' => [''];
        yield 'standard secret key in test mode' => ['sk_test_abc123'];
        yield 'standard secret key in live mode' => ['sk_live_abc123'];
        yield 'publishable key pasted by mistake' => ['pk_test_abc123'];
        yield 'webhook signing secret pasted by mistake' => ['whsec_abc123'];
        yield 'random text' => ['random'];
    }

    /** @return iterable<string, array{string}> */
    public static function acceptedPublishableKeyProvider(): iterable
    {
        yield 'publishable key in test mode' => ['pk_test_abc123'];
        yield 'publishable key in live mode' => ['pk_live_abc123'];
    }

    /** @return iterable<string, array{string}> */
    public static function rejectedPublishableKeyProvider(): iterable
    {
        yield 'empty string' => [''];
        yield 'restricted key pasted by mistake' => ['rk_test_abc123'];
        yield 'standard secret key pasted by mistake' => ['sk_test_abc123'];
        yield 'random text' => ['random'];
    }

    /** @return list<\Symfony\Component\Validator\Constraint> */
    private function secretKeyConstraints(): array
    {
        return [
            new NotBlank(),
            new Regex(['pattern' => StripeGatewayConfigurationType::SECRET_KEY_PATTERN]),
        ];
    }

    /** @return list<\Symfony\Component\Validator\Constraint> */
    private function publishableKeyConstraints(): array
    {
        return [
            new NotBlank(),
            new Regex(['pattern' => StripeGatewayConfigurationType::PUBLISHABLE_KEY_PATTERN]),
        ];
    }
}
