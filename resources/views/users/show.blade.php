<x-app-layout>
    <x-slot name="header">
        <h2>{{ $user->name }}</h2>
    </x-slot>

    <p>Email: {{ $user->email }}</p>
    <p>Role: {{ $user->role->label() }}</p>

    <p><a href="{{ route('users.edit', $user) }}">Edit</a></p>
    <p><a href="{{ route('users.index') }}">Back</a></p>
</x-app-layout>