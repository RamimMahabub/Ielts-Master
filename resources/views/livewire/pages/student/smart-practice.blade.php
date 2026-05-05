<div>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Smart Practice</h2>
        <p class="text-sm text-slate-500 mt-1">Guided study resources selected from your recent weak sections.</p>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Recommended Focus</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $weaknessReport['summary'] ?? 'Complete more mock tests to unlock recommendations.' }}</p>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @forelse(($weaknessReport['weakest_modules'] ?? []) as $weakness)
                    <article class="rounded-xl border border-rose-100 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-950/20">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-rose-900 dark:text-rose-100">{{ $weakness['label'] }}</p>
                            <span class="text-xs font-bold text-rose-700 dark:text-rose-200">Band {{ number_format($weakness['score'], 1) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-rose-800 dark:text-rose-100">{{ $weakness['message'] }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">No weak section detected yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Weakness Heatmap</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">A quick view of which IELTS modules need urgent attention.</p>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                @foreach($weaknessHeatmap as $cell)
                    <article class="rounded-xl border p-4 {{ $cell['level']['class'] }}">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wide">{{ $cell['label'] }}</p>
                            <span class="rounded-full bg-white/70 px-2 py-1 text-[10px] font-bold dark:bg-slate-950/30">{{ $cell['level']['label'] }}</span>
                        </div>
                        <p class="mt-3 text-3xl font-bold">{{ $cell['score'] ? number_format($cell['score'], 1) : '-' }}</p>
                        <p class="mt-1 text-xs opacity-80">Current average band</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Today&apos;s Study Plan</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Use this even when instructor resources are not available yet.</p>
            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                @forelse($studyPlan as $plan)
                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">{{ $plan['label'] }} Drill</p>
                        <ol class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                            @foreach($plan['steps'] as $step)
                                <li class="flex gap-2">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-300">{{ $loop->iteration }}.</span>
                                    <span>{{ $step }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">Complete a mock test to generate a focused study plan.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Micro Tasks</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Tiny drills you can finish quickly before a full practice session.</p>
                </div>
                <span class="w-fit rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200">{{ count($microTasks) }} tasks</span>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($microTasks as $task)
                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">{{ $task['module'] }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $task['task'] }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">Complete a mock test to unlock micro tasks.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">7-Day Recovery Plan</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">A lightweight weekly plan that rotates through your weak modules.</p>

            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-7">
                @forelse($recoveryPlan as $item)
                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $item['day'] }}</p>
                        <p class="mt-2 text-sm font-bold text-slate-900 dark:text-white">{{ $item['module'] }}</p>
                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ $item['focus'] }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">Complete a mock test to unlock a recovery plan.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Guided Practice For Weak Sections</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Instructor resources from Guided Practice are shown when a section average is Band 4.0 or below.
                    </p>
                </div>
                <a href="{{ route('student.guided_practice') }}" class="inline-flex w-fit items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Open Guided Practice Hub</a>
            </div>

            @if(empty($guidedPracticeModules))
                <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-600">
                    No section is currently at Band 4.0 or below. Keep using the targeted question set to improve further.
                </p>
            @else
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    @foreach($guidedPracticeModules as $module)
                        @php
                            $moduleResources = $guidedResources->where('category', $module);
                            $moduleVideos = $guidedVideos->where('category', $module);
                        @endphp

                        <div class="space-y-3">
                            <h4 class="font-bold capitalize text-slate-900 dark:text-white">{{ $module }} Practice Module</h4>

                            <div class="grid grid-cols-1 gap-4">
                                @forelse($moduleResources as $resource)
                                    <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                                        <div class="mb-2 flex items-center justify-between gap-3">
                                            <span class="text-xs font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">{{ $module }} Resource</span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">Instructor: {{ $resource->creator->name ?? 'Instructor' }}</span>
                                        </div>
                                        <p class="whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $resource->content }}</p>
                                    </article>
                                @empty
                                    <p class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500 dark:border-slate-600">No {{ $module }} resources have been added yet.</p>
                                @endforelse
                            </div>

                            @if($moduleVideos->isNotEmpty())
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($moduleVideos as $video)
                                        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $video->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Instructor: {{ $video->creator->name ?? 'Instructor' }}</p>
                                            @if($video->description)
                                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $video->description }}</p>
                                            @endif
                                            <a href="{{ route('guided_videos.play', $video) }}" target="_blank" class="mt-3 inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Watch {{ ucfirst($module) }} Video</a>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
