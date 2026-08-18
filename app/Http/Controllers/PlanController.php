<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Services\Contracts\CourseServiceInterface;
use App\Services\Contracts\PlanServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanServiceInterface $plans,
        private readonly CourseServiceInterface $courses,
    ) {}

    public function index(): View
    {
        return view('plans.index', ['plans' => $this->plans->all()]);
    }

    public function create(): View
    {
        return view('plans.create', ['courses' => $this->courses->all()]);
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->organization_id !== null) {
            $data['organization_id'] = $request->user()->organization_id;
        }

        $this->plans->create($data, $request->input('course_ids', []));

        return redirect()->route('plans.index');
    }

    public function show(int $id): View
    {
        $plan = $this->plans->find($id);
        abort_unless($plan instanceof Plan, 404);

        return view('plans.show', ['plan' => $plan]);
    }

    public function edit(int $id): View
    {
        $plan = $this->plans->find($id);
        abort_unless($plan instanceof Plan, 404);

        return view('plans.edit', [
            'plan' => $plan,
            'courses' => $this->courses->all(),
        ]);
    }

    public function update(UpdatePlanRequest $request, int $id): RedirectResponse
    {
        $plan = $this->plans->find($id);
        abort_unless($plan instanceof Plan, 404);

        $this->plans->update($plan, $request->validated(), $request->input('course_ids'));

        return redirect()->route('plans.show', $plan);
    }

    public function destroy(int $id): RedirectResponse
    {
        $plan = $this->plans->find($id);
        abort_unless($plan instanceof Plan, 404);

        $this->plans->delete($plan);

        return redirect()->route('plans.index');
    }
}
