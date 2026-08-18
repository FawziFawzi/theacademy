<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\Contracts\OrganizationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct(private readonly OrganizationServiceInterface $organizations) {}

    public function index(): View
    {
        return view('organizations.index', ['organizations' => $this->organizations->all()]);
    }

    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $this->organizations->create($request->validated());

        return redirect()->route('organizations.index');
    }

    public function show(int $id): View
    {
        $organization = $this->organizations->find($id);
        abort_unless($organization instanceof Organization, 404);

        return view('organizations.show', ['organization' => $organization]);
    }

    public function edit(int $id): View
    {
        $organization = $this->organizations->find($id);
        abort_unless($organization instanceof Organization, 404);

        return view('organizations.edit', ['organization' => $organization]);
    }

    public function update(UpdateOrganizationRequest $request, int $id): RedirectResponse
    {
        $organization = $this->organizations->find($id);
        abort_unless($organization instanceof Organization, 404);

        $this->organizations->update($organization, $request->validated());

        return redirect()->route('organizations.show', $organization);
    }

    public function destroy(int $id): RedirectResponse
    {
        $organization = $this->organizations->find($id);
        abort_unless($organization instanceof Organization, 404);

        $this->organizations->delete($organization);

        return redirect()->route('organizations.index');
    }
}
