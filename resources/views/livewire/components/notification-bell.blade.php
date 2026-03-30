<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative rounded-xl bg-slate-100 p-2 text-slate-500 transition hover:-translate-y-0.5 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($this->notifications->count() > 0)
            <span class="absolute right-1 top-1 flex h-3 w-3 items-center justify-center rounded-full bg-rose-500 text-[8px] font-bold text-white ring-2 ring-white dark:ring-slate-900">
                {{ $this->notifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-2xl border border-slate-200 bg-white shadow-xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/95" style="display: none;">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Notifications</h3>
            @if($this->notifications->count() > 0)
                <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold">{{ $this->notifications->count() }} new</span>
            @endif
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse($this->notifications as $notification)
                <div class="group relative flex items-start gap-3 border-b border-slate-50 p-4 last:border-0 hover:bg-slate-50 dark:border-slate-800/50 dark:hover:bg-slate-800/30">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $notification->data['type'] === 'new_mock_test' ? 'bg-sky-100 text-sky-600 dark:bg-sky-900/40' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40' }}">
                        @if($notification->data['type'] === 'new_mock_test')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $notification->data['title'] }}</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">{{ $notification->data['message'] }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <a href="{{ $notification->data['link'] }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider hover:underline">View</a>
                            <button wire:click="markAsRead('{{ $notification->id }}')" class="text-[10px] font-medium text-slate-400 hover:text-slate-900 dark:hover:text-slate-100">Dismiss</button>
                        </div>
                    </div>
                    <div class="text-[9px] text-slate-400">{{ $notification->created_at->diffForHumans(null, true) }}</div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No new notifications</p>
                </div>
            @endforelse
        </div>
        @if($this->notifications->count() > 0)
            <div class="p-3 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('student.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400">View All Notifications</a>
            </div>
        @endif
    </div>
</div>
