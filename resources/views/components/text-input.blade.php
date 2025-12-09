@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-viewlix-dark border border-viewlix-border text-white placeholder-gray-500 rounded-xl focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-colors']) }}>
