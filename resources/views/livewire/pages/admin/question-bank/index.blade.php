<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="font-semibold text-2xl leading-tight">Question Bank</h2>
                <p class="text-sm text-slate-500 mt-1">Reusable IELTS content (Listening / Reading / Writing / Speaking).</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($modules as $m)
                    <a href="{{ route('admin.question_bank.create', ['module' => $m]) }}"
                       class="rounded-xl px-3 py-2 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-500">
                        + New {{ ucfirst($m) }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 px-4 py-2 text-emerald-700 dark:text-emerald-300 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
        <div class="flex items-center gap-3 mb-4 flex-wrap">
            <select wire:model.live="module" class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm">
                <option value="">All modules</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                @endforeach
            </select>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search title..."
                   class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm flex-1 min-w-[200px]">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b border-slate-200/70 dark:border-slate-700/70">
                    <tr>
                        <th class="py-2 px-3">Title</th>
                        <th class="py-2 px-3">Module</th>
                        <th class="py-2 px-3">Groups / Qs</th>
                        <th class="py-2 px-3">Created</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="py-2 px-3 font-medium">{{ $item->title }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 capitalize">
                                    {{ $item->module }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-slate-500">
                                {{ $item->groups()->count() }} / {{ \App\Models\Question::whereIn('group_id', $item->groups()->pluck('id'))->count() }}
                            </td>
                            <td class="py-2 px-3 text-slate-500">{{ $item->created_at->diffForHumans() }}</td>
                            <td class="py-2 px-3 text-right space-x-2">
                                <a href="{{ route('admin.question_bank.edit', $item) }}"
                                   class="rounded-lg px-3 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Edit</a>
                                <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Delete this item and all its questions?"
                                        class="rounded-lg px-3 py-1 bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 hover:bg-rose-200">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">No items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</div>
