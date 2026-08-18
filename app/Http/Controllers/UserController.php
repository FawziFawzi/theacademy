<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\Contracts\OrganizationServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly OrganizationServiceInterface $organizations,
    ) {}

    public function index(): View
    {
        return view('users.index', ['users' => $this->users->all()]);
    }

    public function create(): View
    {
        return view('users.create', ['organizations' => $this->organizations->all()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->organization_id !== null) {
            $data['organization_id'] = $request->user()->organization_id;
        }

        $this->users->create($data);

        return redirect()->route('users.index');
    }

    public function show(int $id): View
    {
        $user = $this->users->find($id);
        abort_unless($user instanceof User, 404);

        return view('users.show', ['user' => $user]);
    }

    public function edit(int $id): View
    {
        $user = $this->users->find($id);
        abort_unless($user instanceof User, 404);

        return view('users.edit', [
            'user' => $user,
            'organizations' => $this->organizations->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $user = $this->users->find($id);
        abort_unless($user instanceof User, 404);

        $data = $request->validated();

        if ($request->user()->organization_id !== null) {
            $data['organization_id'] = $request->user()->organization_id;
        }

        $this->users->update($user, $data);

        return redirect()->route('users.show', $user);
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = $this->users->find($id);
        abort_unless($user instanceof User, 404);

        $this->users->delete($user);

        return redirect()->route('users.index');
    }
}
