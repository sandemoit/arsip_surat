@props([
    'expandable' => false,
    'expanded' => true,
    'heading' => null,
    'icon' => null,
])

<?php if ($expandable && $heading): ?>

<ui-disclosure
    {{ $attributes->class('group/disclosure') }}
    @if ($expanded === true) open @endif
    data-flux-navlist-group
    data-expandable
>
    <button
        type="button"
        class="py-5 group/disclosure-button mb-[2px] flex h-10 w-full items-center justify-between rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white lg:h-8 px-3"
    >
        <div class="flex items-center gap-3">
            @if ($icon)
                <flux:icon :name="$icon" class="size-5 shrink-0" />
            @endif
            <span class="text-sm font-medium leading-none">{{ $heading }}</span>
        </div>

        <div class="shrink-0">
            <flux:icon.chevron-down class="hidden size-4 group-data-open/disclosure-button:block" />
            <flux:icon.chevron-right class="block size-4 group-data-open/disclosure-button:hidden" />
        </div>
    </button>

    <div class="relative hidden space-y-[2px] ps-7 data-open:block" @if ($expanded === true) data-open @endif>
        <div class="absolute inset-y-[3px] start-0 ms-4 w-px bg-slate-700"></div>

        {{ $slot }}
    </div>
</ui-disclosure>

<?php elseif ($heading): ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    <div class="px-1 py-2">
        <div class="text-xs leading-none text-slate-500">{{ $heading }}</div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>

<?php else: ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    {{ $slot }}
</div>

<?php endif; ?>
