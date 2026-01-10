@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-gray-900/60 dark:bg-gray-800/60 rounded-xl p-8 shadow-lg text-white ' . $class]) }}>
    {{ $slot }}
</div>
