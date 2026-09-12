<?php

namespace App\Http\Controllers;

use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\Contracts\PlanServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use Illuminate\Http\RedirectResponse;

class PaymentCheckoutController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly SubscriptionServiceInterface $subscriptions,
        private readonly PlanServiceInterface $plans,
    ) {}

    public function checkout(int $plan): RedirectResponse
    {
        $planModel = $this->plans->find($plan);
        abort_unless($planModel !== null, 404);

        $subscription = $this->subscriptions->startCheckout(request()->user(), $planModel);

        $result = $this->gateway->initiate($subscription);

        $this->subscriptions->attachGatewayReference($subscription, $result->gatewayReference);

        return redirect()->away($result->redirectUrl);
    }
}
