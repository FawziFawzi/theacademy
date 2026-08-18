<x-app-layout>
    <x-slot name="header">
        <h2>Organizations</h2>
    </x-slot>

    <p><a href="{{ route('organizations.create') }}">New organization</a></p>

    <ul>
        @foreach ($organizations as $organization)
            <li>
                <a href="{{ route('organizations.show', $organization) }}">{{ $organization->name }}</a>
                — {{ $organization->status->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>