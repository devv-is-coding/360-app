<x-layouts::auth :title="__('Super admin sign up')">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-3">
            <flux:badge size="sm" color="amber" icon="shield-check" class="self-center">
                {{ __('Super admin') }}
            </flux:badge>

            <x-auth-header
                :title="__('Create a super admin account')"
                :description="__('This account has unrestricted access to every tenant and platform setting')"
            />
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- First Name -->
            <flux:input
                name="first_name"
                :label="__('First name')"
                :value="old('first_name')"
                type="text"
                required
                autofocus
                autocomplete="given-name"
                :placeholder="__('First name')"
            />

            <!-- Middle Name -->
            <flux:input
                name="middle_name"
                :label="__('Middle name')"
                :value="old('middle_name')"
                type="text"
                autocomplete="additional-name"
                :placeholder="__('Optional')"
            />

            <!-- Last Name -->
            <flux:input
                name="last_name"
                :label="__('Last name')"
                :value="old('last_name')"
                type="text"
                required
                autocomplete="family-name"
                :placeholder="__('Last name')"
            />

            <!-- Username -->
            <flux:input
                name="username"
                :label="__('Username')"
                :value="old('username')"
                type="text"
                required
                autocomplete="username"
                :description="__('Letters, numbers, dashes and underscores only.')"
                placeholder="superadmin"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="admin@example.com"
            />

            <!-- Contact Number -->
            <flux:input
                name="contact_number"
                :label="__('Contact number')"
                :value="old('contact_number')"
                type="tel"
                autocomplete="tel"
                :placeholder="__('Optional')"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:callout variant="warning" icon="exclamation-triangle" inline>
                <flux:callout.text>
                    {{ __('Anyone who can reach this page can create a super admin account. Restrict access to this domain before going live.') }}
                </flux:callout.text>
            </flux:callout>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create super admin account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
