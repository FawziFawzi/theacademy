<x-app-layout>
    <x-form-shell
        title="Profile"
        subtitle="Manage your account's profile information, password, and data."
    >
        <div class="space-y-8">
            <div class="space-y-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="space-y-6 pt-2 border-t border-line">
                <div class="pt-6 space-y-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="space-y-6 pt-2 border-t border-line">
                <div class="pt-6 space-y-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </x-form-shell>
</x-app-layout>
