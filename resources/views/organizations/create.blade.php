<x-app-layout>
    <x-slot name="header">
        <h2>New organization</h2>
    </x-slot>

    <form method="POST" action="{{ route('organizations.store') }}">
        @csrf

        <p><label>Name <input type="text" name="name" value="{{ old('name') }}"></label></p>
        <p><label>Address <input type="text" name="address" value="{{ old('address') }}"></label></p>
        <p><label>Email <input type="email" name="email" value="{{ old('email') }}"></label></p>
        <p><label>Phone <input type="text" name="phone" value="{{ old('phone') }}"></label></p>
        <p><button type="submit">Create</button></p>
    </form>

    <p><a href="{{ route('organizations.index') }}">Back</a></p>
</x-app-layout>