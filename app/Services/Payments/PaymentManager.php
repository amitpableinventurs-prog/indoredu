<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentManager
{
    /** @var array<string, class-string<PaymentGateway>> */
    protected array $gateways = [
        'stripe' => StripeGateway::class,
        'paypal' => PaypalGateway::class,
    ];

    public function gateway(?string $name = null): PaymentGateway
    {
        $name ??= config('payments.default_gateway', 'stripe');

        if (! isset($this->gateways[$name])) {
            throw new InvalidArgumentException("Unknown payment gateway [{$name}].");
        }

        return app($this->gateways[$name]);
    }

    /**
     * @return array<int, string>
     */
    public function availableGateways(): array
    {
        return array_keys($this->gateways);
    }
}
