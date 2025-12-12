<section>
    <header>
        <h2 class="text-lg font-medium text-white">Avatar</h2>
        <p class="mt-1 text-sm text-gray-400">Upload a new profile picture. Images are stored securely in Firebase Storage.</p>
    </header>
    <form id="avatar-upload-form" class="mt-4" enctype="multipart/form-data" method="POST" action="{{ route('profile.avatar') }}">
        @csrf
        <div class="flex flex-col items-center gap-4 w-full">
            <div class="flex justify-center w-full mb-2">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" class="w-20 h-20 rounded-full object-cover border border-viewlix-border" alt="Avatar Preview">
                @else
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white text-2xl font-bold uppercase">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="w-full flex flex-col items-center">
                <label for="avatar" class="block mb-2 text-sm font-medium text-gray-300 text-center">Profile Image</label>
                <div class="hs-file-upload relative flex flex-col items-center justify-center w-full max-w-xs p-4 border-2 border-dashed border-gray-600 rounded-xl bg-viewlix-gray/50 transition hover:border-viewlix-red group">
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="if(this.files[0] && this.files[0].size > 5*1024*1024){ alert('File size must be 5MB or less.'); this.value=''; }">
                    <div class="flex flex-col items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera-icon lucide-camera"><path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z"/><circle cx="12" cy="13" r="3"/></svg>
                        <span class="text-sm text-gray-400">Drag & drop or <span class="text-viewlix-red font-semibold">browse</span></span>
                        <span class="text-xs text-gray-500 mt-1">Max size: 5MB</span>
                    </div>
                </div>
            </div>
        </div>
    <div class="flex flex-col items-center justify-center gap-2 sm:flex-row sm:items-center sm:gap-4 mt-4 w-full">
            <button type="submit" class="w-full sm:w-auto bg-viewlix-red hover:bg-viewlix-red-dark text-white px-4 py-2 rounded-xl font-semibold">
                Upload
            </button>
            @error('avatar')
                <span class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
    </form>
</section>
