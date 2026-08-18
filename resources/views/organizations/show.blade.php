<x-app-layout>
    <x-slot name="header">
        <h2>{{ $organization->name }}</h2>
    </x-slot>

    <p>Email: {{ $organization->email }}</p>
    <p>Status: {{ $organization->status->label() }}</p>

    <p><a href="{{ route('organizations.edit', $organization) }}">Edit</a></p>
    <p><a href="{{ route('organizations.index') }}">Back</a></p>
</x-app-layout>