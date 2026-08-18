<x-app-layout>
    <x-slot name="header">
        <h2>Edit {{ $organization->name }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('organizations.update', $organization) }}">
        @csrf
        @method('PATCH')

        <p><label>Name <input type="text" name="name" value="{{ old('name', $organization->name) }}"></label></p>
        <p><label>Address <input type="text" name="address" value="{{ old('address', $organization->address) }}"></label></p>
        <p><label>Email <input type="email" name="email" value="{{ old('email', $organization->email) }}"></label></p>
        <p><label>Phone <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}"></label></p>
        <p><button type="submit">Save</button></p>
    </form>

    <p><a href="{{ route('organizations.show', $organization) }}">Back</a></p>
</x-app-layout>