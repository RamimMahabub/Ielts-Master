<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Instructor Verification</h2>
        <p class="text-sm text-slate-500 mt-1">Approve or reject instructor registration requests.</p>
    </x-slot>

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6">
        @if (session()->has('message'))
            <div class="mb-4 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 px-4 py-2 text-sm">{{ session('message') }}</div>
        @endif

        @if($pendingInstructors->isEmpty())
            <p class="text-slate-500">No pending instructor requests.</p>
        @else
            <div class="space-y-3">
                @foreach($pendingInstructors as $user)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ $user->name }}</p>
                            <p class="text-sm text-slate-500">{{ $user->email }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="approve({{ $user->id }})" class="rounded-lg px-3 py-2 text-sm bg-emerald-600 text-white hover:bg-emerald-700">Approve</button>
                            <button wire:click="reject({{ $user->id }})" class="rounded-lg px-3 py-2 text-sm bg-rose-600 text-white hover:bg-rose-700">Reject</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
