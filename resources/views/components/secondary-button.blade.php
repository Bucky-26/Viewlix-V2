<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl font-semibold text-sm text-gray-300 uppercase tracking-widest hover:bg-viewlix-gray-light hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red focus:ring-offset-2 focus:ring-offset-viewlix-dark disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
