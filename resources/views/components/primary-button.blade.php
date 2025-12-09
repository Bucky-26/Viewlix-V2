<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-viewlix-red border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-viewlix-red-dark focus:bg-viewlix-red-dark active:bg-viewlix-red-dark focus:outline-none focus:ring-2 focus:ring-viewlix-red focus:ring-offset-2 focus:ring-offset-viewlix-dark transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
