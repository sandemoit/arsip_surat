@php $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware([ 'variant' ])

@props([
    'iconVariant' => 'outline',
    'iconTrailing' => null,
    'badgeColor' => null,
    'variant' => null,
    'iconDot' => null,
    'accent' => true,
    'badge' => null,
    'icon' => null,
])

@php
$square ??= $slot->isEmpty();
$iconClasses = Flux::classes($square ? 'size-5!' : 'size-5!');

$classes = Flux::classes()
    ->add('h-10 lg:h-8 relative flex items-center gap-3 rounded-lg')
    ->add($square ? 'px-2.5!' : '')
    ->add('py-5 text-start w-full px-3 my-px')
    ->add('text-slate-300')
    ->add(match ($variant) {
        'outline' => match ($accent) {
            true => [
                'data-current:text-white data-current:bg-slate-800 data-current:border data-current:border-slate-700',
                'hover:text-white hover:bg-slate-800',
                'border border-transparent',
            ],
            false => [
                'data-current:text-white data-current:bg-slate-800 data-current:border data-current:border-slate-700',
                'hover:text-white hover:bg-slate-800',
            ],
        },
        default => match ($accent) {
            true => [
                'data-current:text-white data-current:bg-slate-800',
                'hover:text-white hover:bg-slate-800',
            ],
            false => [
                'data-current:text-white data-current:bg-slate-800',
                'hover:text-white hover:bg-slate-800',
            ],
        },
    })
    ;
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navlist-item>
    <?php if ($icon) { ?>
        <div class="relative">
            <?php if (is_string($icon) && $icon !== '') { ?>
                <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
            <?php } else { ?>
                {{ $icon }}
            <?php } ?>

            <?php if ($iconDot) { ?>
                <div class="absolute top-[-2px] end-[-2px]">
                    <div class="size-[6px] rounded-full bg-slate-400"></div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if ($slot->isNotEmpty()) { ?>
        <div class="flex-1 text-sm font-medium leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block" data-content>{{ $slot }}</div>
    <?php } ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== '') { ?>
        <flux:icon :icon="$iconTrailing" :variant="$iconVariant" class="size-4!" />
    <?php } elseif ($iconTrailing) { ?>
        {{ $iconTrailing }}
    <?php } ?>

    <?php if (isset($badge) && $badge !== '') { ?>
        <?php $badgeAttributes = Flux::attributesAfter('badge:', $attributes, ['color' => $badgeColor]); ?>
        <flux:navlist.badge :attributes="$badgeAttributes">{{ $badge }}</flux:navlist.badge>
    <?php } ?>
</flux:button-or-link>
