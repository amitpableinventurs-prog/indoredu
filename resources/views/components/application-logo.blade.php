@props(['rounded' => 'rounded-lg'])

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center {$rounded} overflow-hidden shrink-0"]) }}>
    <img src="{{ asset('images/logo-icon.png') }}" alt="IndorEdu" class="w-full h-full object-contain">
</span>
