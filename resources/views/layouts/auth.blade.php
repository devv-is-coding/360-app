<x-layouts::auth.split
    :title="$title ?? null"
    :panel-title="$panelTitle ?? null"
    :panel-description="$panelDescription ?? null"
    :panel-notice="$panelNotice ?? null"
>
    {{ $slot }}
</x-layouts::auth.split>
