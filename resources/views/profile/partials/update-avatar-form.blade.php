<section class="w-full">
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-white/5">
        <div>
            <h2 class="text-lg font-bold text-white">Profile Photo</h2>
            <p class="mt-1 text-xs text-gray-400">
                Upload a personalized photo for your Viewlix account profile.
            </p>
        </div>

        @if(Auth::user()->avatar)
            <form method="POST" action="{{ route('profile.avatar.delete') }}" onsubmit="return confirm('Are you sure you want to remove your profile photo?');">
                @csrf
                @method('delete')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold border border-red-500/20 hover:border-red-500/40 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remove Photo
                </button>
            </form>
        @endif
    </header>

    <form id="avatar-upload-form" class="mt-6" enctype="multipart/form-data" method="POST" action="{{ route('profile.avatar') }}">
        @csrf

        <div class="flex flex-col md:flex-row items-center gap-8 w-full">
            {{-- Avatar Live Preview Box --}}
            <div class="flex flex-col items-center flex-shrink-0">
                <div class="relative group">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl p-1 bg-gradient-to-tr from-viewlix-red via-blue-500 to-indigo-500 shadow-[0_0_25px_rgba(0,74,173,0.35)]">
                        <div id="avatar-preview-wrapper" class="w-full h-full rounded-[22px] overflow-hidden bg-[#0d0e17] flex items-center justify-center">
                            @if(Auth::user()->avatar)
                                <img id="avatar-preview-img" src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover" alt="Avatar Preview">
                            @else
                                <div id="avatar-default-initials" class="w-full h-full bg-gradient-to-tr from-[#0f172a] to-[#1e1b4b] flex items-center justify-center text-white text-3xl font-extrabold uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <img id="avatar-preview-img" class="w-full h-full object-cover hidden" alt="Avatar Preview">
                            @endif
                        </div>
                    </div>

                    {{-- Live Selection Pulse Indicator --}}
                    <span id="avatar-selected-badge" class="hidden absolute -top-1 -right-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-lg animate-bounce">
                        New!
                    </span>
                </div>
                <span class="text-[11px] text-gray-500 mt-2 font-medium">Photo Preview</span>
            </div>

            {{-- Drag-and-drop / File Selector --}}
            <div class="w-full flex-1">
                <div id="avatar-drop-zone" 
                     class="relative flex flex-col items-center justify-center w-full p-6 sm:p-8 border-2 border-dashed border-white/15 hover:border-viewlix-red/70 rounded-2xl bg-white/[0.02] hover:bg-white/[0.04] transition-all cursor-pointer group">
                    
                    <input type="file" 
                           id="avatar-file-input" 
                           name="avatar" 
                           accept="image/jpeg,image/png,image/webp,image/gif" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                           required 
                           onchange="handleAvatarFileSelect(this)">

                    <div class="flex flex-col items-center pointer-events-none text-center">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 group-hover:scale-110 group-hover:bg-blue-500/20 flex items-center justify-center mb-3 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-white">
                            Drag & drop your new image or <span class="text-blue-400 underline decoration-blue-400/50 underline-offset-2">browse files</span>
                        </span>
                        <span class="text-xs text-gray-400 mt-1.5">Supports PNG, JPG, WebP or GIF (Up to 5MB)</span>
                        <span id="selected-filename-label" class="text-xs font-mono text-emerald-400 mt-2 hidden"></span>
                    </div>
                </div>

                @error('avatar')
                    <p class="text-xs font-semibold text-rose-400 mt-2">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end gap-3 mt-4">
                    <button type="submit" 
                            id="avatar-submit-btn"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Photo
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    function handleAvatarFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // 5MB Limit check
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds the 5MB limit. Please choose a smaller image.');
            input.value = '';
            return;
        }

        // Update selected file label
        const filenameLabel = document.getElementById('selected-filename-label');
        if (filenameLabel) {
            filenameLabel.textContent = 'Selected: ' + file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
            filenameLabel.classList.remove('hidden');
        }

        // Instant preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('avatar-preview-img');
            const defaultInitials = document.getElementById('avatar-default-initials');
            const badge = document.getElementById('avatar-selected-badge');

            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            }
            if (defaultInitials) {
                defaultInitials.classList.add('hidden');
            }
            if (badge) {
                badge.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
</script>
