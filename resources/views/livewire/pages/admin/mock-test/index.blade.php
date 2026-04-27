<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="font-semibold text-2xl leading-tight">Mock Test Builder</h2>
                <p class="text-sm text-slate-500 mt-1">Compose Listening + Reading + Writing + Speaking modules into a complete IELTS mock test.</p>
            </div>
            <a href="{{ route('admin.mock_test.create') }}" class="rounded-xl px-3 py-2 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-500">+ New Mock Test</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 px-4 py-2 text-emerald-700 dark:text-emerald-300 text-sm">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-5">
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search title..."
                   class="w-full md:w-1/3 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b border-slate-200/70 dark:border-slate-700/70">
                    <tr>
                        <th class="py-2 px-3">Title</th>
                        <th class="py-2 px-3">Type</th>
                        <th class="py-2 px-3">Modules</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Created</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $t)
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="py-2 px-3 font-medium">{{ $t->title }}</td>
                            <td class="py-2 px-3 capitalize">{{ $t->test_type }}</td>
                            <td class="py-2 px-3 text-slate-500">{{ $t->modules_count }}</td>
                            <td class="py-2 px-3">
                                <button wire:click="togglePublish({{ $t->id }})"
                                        class="px-2 py-1 rounded-full text-xs {{ $t->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ $t->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </td>
                            <td class="py-2 px-3 text-slate-500">{{ $t->created_at->diffForHumans() }}</td>
                            <td class="py-2 px-3 text-right space-x-2">
                                <a href="{{ route('admin.mock_test.edit', $t) }}"
                                   class="rounded-lg px-3 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40">Edit</a>
                                <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this mock test?"
                                        class="rounded-lg px-3 py-1 bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No mock tests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tests->links() }}</div>
    </div>
</div>
