<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\Contracts\PlanServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptions,
        private readonly PlanServiceInterface $plans,
    ) {}

    public function index(): View
    {
        return view('subscriptions.index', ['subscriptions' => $this->subscriptions->visibleTo(request()->user())]);
    }

    public function create(): View
    {
        return view('subscriptions.create', ['plans' => $this->plans->all()]);
    }

    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $plan = $this->plans->find($request->integer('plan_id'));
        abort_unless($plan !== null, 404);

        $subscription = $this->subscriptions->subscribe($request->user(), $plan);

        return redirect()->route('subscriptions.show', $subscription);
    }

    public function show(int $id): View
    {
        $subscription = $this->subscriptions->findVisibleTo(request()->user(), $id);
        abort_unless($subscription instanceof Subscription, 404);

        return view('subscriptions.show', ['subscription' => $subscription]);
    }

    public function cancel(int $id): RedirectResponse
    {
        $subscription = $this->subscriptions->findVisibleTo(request()->user(), $id);
        abort_unless($subscription instanceof Subscription, 404);

        $this->subscriptions->cancel(request()->user(), $subscription);

        return redirect()->route('subscriptions.show', $subscription);
    }

    public function edit(int $id): View
    {
        $subscription = $this->subscriptions->findVisibleTo(request()->user(), $id);
        abort_unless($subscription instanceof Subscription, 404);

        return view('subscriptions.edit', ['subscription' => $subscription]);
    }

    public function update(UpdateSubscriptionRequest $request, int $id): RedirectResponse
    {
        $subscription = $this->subscriptions->findVisibleTo($request->user(), $id);
        abort_unless($subscription instanceof Subscription, 404);

        $this->subscriptions->update($request->user(), $subscription, $request->validated());

        return redirect()->route('subscriptions.show', $subscription);
    }

    public function destroy(int $id): RedirectResponse
    {
        $subscription = $this->subscriptions->findVisibleTo(request()->user(), $id);
        abort_unless($subscription instanceof Subscription, 404);

        $this->subscriptions->delete(request()->user(), $subscription);

        return redirect()->route('subscriptions.index');
    }
}
