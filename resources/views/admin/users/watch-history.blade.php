@extends('layouts.admin')

@section('title', 'User Watch History')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">Watch History for {{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ $user->email }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if(isset($firebaseConfigured) && $firebaseConfigured)
                <span class="inline-flex items-center gap-x-2 rounded-lg px-3 py-2 bg-green-600/10 text-green-400 text-sm">Firebase: Connected</span>
            @else
                <span class="inline-flex items-center gap-x-2 rounded-lg px-3 py-2 bg-yellow-600/10 text-yellow-400 text-sm">Firebase: Not configured</span>
            @endif

            {{-- Show firebase uuid so admin can verify path in Firebase console --}}
            @if(!empty($firebaseUuid))
                <span class="inline-flex items-center gap-x-2 rounded-lg px-3 py-2 bg-viewlix-gray text-gray-300 text-sm">UUID: <code class="ml-2 text-xs text-gray-200">{{ $firebaseUuid }}</code></span>
            @endif

            <a href="{{ route('admin.users.edit', $user) }}" class="py-2 px-3 rounded-lg bg-viewlix-gray text-gray-300 border border-viewlix-border">Back to user</a>
        </div>
    </div>

    @if($items->count())
        <div class="rounded-xl overflow-hidden bg-viewlix-dark border border-viewlix-border">
            <div class="p-4">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase">
                            <th class="p-3">Media ID</th>
                            <th class="p-3">Title / Info</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Progress</th>
                            <th class="p-3">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="border-t border-viewlix-border">
                                <td class="p-3 align-top">{{ $item['id'] }}</td>
                                <td class="p-3 align-top">
                                    @if(!empty($item['title']))
                                        <div class="text-sm text-white font-medium">{{ $item['title'] }}</div>
                                    @endif
                                    @if(!empty($item['episode_title']))
                                        <div class="text-xs text-gray-400">Episode: {{ $item['episode_title'] }}</div>
                                    @endif
                                </td>
                                <td class="p-3 align-top">{{ $item['type'] ?? ($item['media_type'] ?? '-') }}</td>
                                <td class="p-3 align-top">
                                    @if(!empty($item['progress']) && is_array($item['progress']))
                                        @php
                                            $watched = $item['progress']['watched'] ?? null;
                                            $duration = $item['progress']['duration'] ?? null;
                                        @endphp
                                        @if($watched && $duration)
                                            {{ round(($watched / $duration) * 100, 1) }}%
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 align-top text-sm text-gray-400">@if(!empty($item['last_updated'])){{ \Carbon\Carbon::createFromTimestampMs($item['last_updated'])->toDayDateTimeString() }}@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-xl p-6 bg-viewlix-dark border border-viewlix-border">
            <h3 class="text-lg text-white">No watch history found</h3>
            <p class="text-gray-400">This user has no watch history in Firebase.</p>

            @if(!empty($rawData))
                <div class="mt-4">
                    <button id="toggle-raw-json" class="py-2 px-3 rounded-lg bg-viewlix-gray text-gray-300 border border-viewlix-border">Show raw Firebase JSON</button>
                    <pre id="raw-json" class="mt-3 p-3 rounded bg-[#0b0b0f] text-xs text-gray-200 overflow-auto" style="display:none; max-height:420px;">{{ json_encode($rawData, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @else
                {{-- If firebase is not configured, show hint how to enable --}}
                @if(empty($rawData) && empty($firebaseConfigured))
                    <div class="mt-4 text-sm text-gray-400">
                        To show watch history here, ensure the Firebase service account is present at <code>storage/app/firebase/service-account.json</code> and Realtime Database is enabled. The client must sign in to Firebase using the token from <code>/firebase-token</code> so data is written under <code>users/{firebase_uuid}/watchProgress</code>.
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('toggle-raw-json');
    const pre = document.getElementById('raw-json');
    if(!btn || !pre) return;
    btn.addEventListener('click', function(){
        if(pre.style.display === 'none'){
            pre.style.display = 'block';
            btn.textContent = 'Hide raw Firebase JSON';
        } else {
            pre.style.display = 'none';
            btn.textContent = 'Show raw Firebase JSON';
        }
    });
});
</script>
@endpush
