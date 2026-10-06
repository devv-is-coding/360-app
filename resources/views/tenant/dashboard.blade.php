@php($title = __('Dashboard'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <header class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
            <div class="flex items-center gap-3">
                <x-app-logo-icon class="size-7 fill-current text-black dark:text-white" />
                <flux:heading size="lg">{{ tenant('name') ?? tenant('id') }}</flux:heading>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" icon="arrow-right-start-on-rectangle" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </header>

        <main class="mx-auto flex max-w-5xl flex-col gap-2 p-6">
            <flux:heading size="xl">{{ __('Welcome, :name', ['name' => auth()->user()->first_name]) }}</flux:heading>

            <flux:text>
                {{ __('You are signed in as :role of :tenant.', [
                    'role' => auth()->user()->role->label(),
                    'tenant' => tenant('name') ?? tenant('id'),
                ]) }}
            </flux:text>
        </main>

        @fluxScripts
    </body>
</html>
