<div class="space-y-6">
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Guided Practice Hub</h2>
        <p class="text-sm text-slate-500 mt-1">Choose a skill category, study instructor resources, and track completion.</p>
    </x-slot>

    <section class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 space-y-5">
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $category)
                <button
                    wire:click="setCategory('{{ $category }}')"
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition {{ $selectedCategory === $category ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    {{ ucfirst($category) }}
                </button>
            @endforeach
        </div>

        @if(session('status'))
            <p class="rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 text-sm px-3 py-2">{{ session('status') }}</p>
        @endif

        <div class="space-y-3">
            <h3 class="font-semibold text-lg">{{ ucfirst($selectedCategory) }} Resources</h3>
            @forelse($resources as $resource)
                <article class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-3">
                    <p class="text-sm leading-6 text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $resource->content }}</p>
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">Instructor: {{ $resource->creator->name ?? 'Instructor' }}</p>
                        @if(in_array($resource->id, $completedResourceIds, true))
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">Completed</span>
                        @else
                            <button wire:click="completeResource({{ $resource->id }})" type="button" class="rounded-lg bg-emerald-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-emerald-500">Complete</button>
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-sm text-slate-500">No resources available in this category yet.</p>
            @endforelse
        </div>

        <div class="border-t border-slate-200/70 dark:border-slate-700 pt-5 space-y-3">
            <h3 class="font-semibold text-lg">{{ ucfirst($selectedCategory) }} Videos</h3>
            @forelse($categoryVideos as $video)
                <article class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-3">
                    <div class="flex flex-col gap-1">
                        <p class="font-semibold">{{ $video->title }}</p>
                        <p class="text-xs text-slate-500">Instructor: {{ $video->creator->name ?? 'Instructor' }}</p>
                        @if($video->description)
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $video->description }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('guided_videos.play', $video) }}" target="_blank" class="rounded-lg bg-indigo-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-indigo-500">Watch {{ ucfirst($selectedCategory) }} Video</a>
                    </div>
                </article>
            @empty
                <p class="text-sm text-slate-500">No {{ $selectedCategory }} videos available yet.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 space-y-4">
        <div>
            <h3 class="font-semibold text-lg">Class Recordings</h3>
            <p class="text-sm text-slate-500">Watch instructor recordings and choose complete or watch later.</p>
        </div>

        @if(session('recording_status'))
            <p class="rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 text-sm px-3 py-2">{{ session('recording_status') }}</p>
        @endif

        <div class="space-y-3">
            @forelse($recordings as $recording)
                <article class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-3">
                    <div class="flex flex-col gap-1">
                        <p class="font-semibold">{{ $recording->title }}</p>
                        <p class="text-xs text-slate-500">Instructor: {{ $recording->creator->name ?? 'Instructor' }}</p>
                        @if($recording->description)
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $recording->description }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('recordings.play', $recording) }}" target="_blank" class="rounded-lg bg-indigo-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-indigo-500">Watch Now</a>
                        <button wire:click="markRecordingWatchLater({{ $recording->id }})" type="button" class="rounded-lg bg-amber-500 text-white px-3 py-1.5 text-xs font-semibold hover:bg-amber-400">Watch Later</button>
                        <button wire:click="markRecordingCompleted({{ $recording->id }})" type="button" class="rounded-lg bg-emerald-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-emerald-500">Complete</button>

                        @if(($recordingStatuses[$recording->id] ?? null) === 'completed')
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">Completed</span>
                        @elseif(($recordingStatuses[$recording->id] ?? null) === 'watch_later')
                            <span class="text-xs font-semibold rounded-full px-2.5 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">Watch Later</span>
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-sm text-slate-500">No class recordings available yet.</p>
            @endforelse
        </div>
    </section>
</div>
