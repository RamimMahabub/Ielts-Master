<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Instructor Verification Pending</h2>
    </x-slot>

    <div class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-8">
        <h3 class="text-lg font-semibold mb-2">Your instructor account is under review.</h3>
        <p class="text-slate-600 dark:text-slate-300">An admin needs to approve your request before instructor dashboards and tools are unlocked.</p>
        <p class="mt-4 text-sm text-slate-500">Status: <span class="font-semibold uppercase">{{ auth()->user()->instructor_status }}</span></p>
    </div>
</div>
