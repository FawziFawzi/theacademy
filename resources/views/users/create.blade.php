<x-app-layout>
    <x-slot name="header">
        <h2>New user</h2>
    </x-slot>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <p><label>Name <input type="text" name="name" value="{{ old('name') }}"></label></p>
        <p><label>Email <input type="email" name="email" value="{{ old('email') }}"></label></p>
        <p><label>Password <input type="password" name="password"></label></p>
        <p><label>Confirm password <input type="password" name="password_confirmation"></label></p>
        <p><button type="submit">Create</button></p>
    </form>

    <p><a href="{{ route('users.index') }}">Back</a></p>
</x-app-layout>