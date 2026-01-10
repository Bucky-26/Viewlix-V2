{{-- Logo component --}}
{{-- Renders the logo image served at /logo.png. Usage: <x-logo /> or <x-logo class="w-8 h-8" alt="..." /> --}}
<img src="{{ route('logo') }}"
     alt="Viewlix logo"
     {{ $attributes->merge(['class' => 'w-8 h-8']) }}
/> 
