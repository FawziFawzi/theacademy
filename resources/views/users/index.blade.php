<x-app-layout>
    <x-slot name="header">
        <h2>Users</h2>
    </x-slot>

    <p><a href="{{ route('users.create') }}">New user</a></p>

    <ul>
        @foreach ($users as $user)
            <li>
                <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                — {{ $user->role->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>