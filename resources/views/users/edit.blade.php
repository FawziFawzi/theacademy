<x-app-layout>
    <x-slot name="header">
        <h2>Edit {{ $user->name }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PATCH')

        <p><label>Name <input type="text" name="name" value="{{ old('name', $user->name) }}"></label></p>
        <p><label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}"></label></p>
        <p><label>Password <input type="password" name="password"></label></p>
        <p><label>Confirm password <input type="password" name="password_confirmation"></label></p>
        <p><button type="submit">Save</button></p>
    </form>

    <p><a href="{{ route('users.show', $user) }}">Back</a></p>
</x-app-layout>