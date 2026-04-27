<div class="space-y-6">
    <x-slot name="header">
        <h2 class="font-semibold text-2xl leading-tight">Guided Practice Hub</h2>
        <p class="text-sm text-slate-500 mt-1">Create category-wise learning resources and upload class recordings for students.</p>
    </x-slot>

    <section class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 space-y-5">
        <div class="flex flex-wrap items-center gap-2">
            @foreach($categories as $category)
                <button
                    wire:click="setCategory('{{ $category }}')"
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
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">{{ ucfirst($selectedCategory) }} Resources</h3>
                <button wire:click="addResourceInput" type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white hover:bg-indigo-500" title="Add new resource textbox">+</button>
            </div>

            @foreach($resourceInputs as $index => $resourceInput)
                <div class="space-y-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Resource {{ $index + 1 }}</label>
                    <textarea wire:model="resourceInputs.{{ $index }}" rows="4" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm" placeholder="Write instructions, notes, links, tasks, or homework for students..."></textarea>
                    <div class="flex justify-end">
                        <button wire:click="removeResourceInput({{ $index }})" type="button" class="text-xs text-rose-600 hover:underline">Remove</button>
                    </div>
                    @error('resourceInputs.'.$index)
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button wire:click="saveResources" type="button" class="rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm font-semibold hover:bg-emerald-500">Save Resources</button>
        </div>

        <div class="border-t border-slate-200/70 dark:border-slate-700 pt-5 space-y-4">
            <div>
                <h3 class="font-semibold text-lg">{{ ucfirst($selectedCategory) }} Videos</h3>
                <p class="text-sm text-slate-500">Upload module-specific videos for {{ $selectedCategory }}. Same upload/compression logic as class recordings.</p>
            </div>

            @if(session('category_video_status'))
                <p class="rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 text-sm px-3 py-2">{{ session('category_video_status') }}</p>
            @endif

            <form wire:submit="uploadCategoryVideo" class="space-y-4" wire:key="category-video-upload-form-{{ $selectedCategory }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Video Title</label>
                        <input wire:model.live="categoryVideoTitle" type="text" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm" placeholder="Example: {{ ucfirst($selectedCategory) }} strategy session - week 1">
                        @error('categoryVideoTitle')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Video File</label>
                        <input wire:model="categoryVideoFile" type="file" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm">
                        @error('categoryVideoFile')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Description (Optional)</label>
                        <textarea wire:model.live="categoryVideoDescription" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm" placeholder="What does this {{ $selectedCategory }} video teach?"></textarea>
                    </div>
                </div>

                <p wire:loading wire:target="categoryVideoFile" class="text-xs text-indigo-600">Uploading file to temporary storage...</p>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="categoryVideoFile,uploadCategoryVideo"
                        class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="uploadCategoryVideo">Upload {{ ucfirst($selectedCategory) }} Video</span>
                    <span wire:loading wire:target="uploadCategoryVideo">Processing...</span>
                </button>
            </form>

            <div class="space-y-3">
                <h4 class="font-semibold">My {{ ucfirst($selectedCategory) }} Videos</h4>
                @forelse($categoryVideos as $video)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold">{{ $video->title }}</p>
                            <p class="text-xs text-slate-500">Uploaded {{ $video->created_at->diffForHumans() }}</p>
                            @if($video->description)
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $video->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('guided_videos.play', $video) }}" target="_blank" class="text-sm text-indigo-600 hover:underline">Open</a>
                            <button wire:click="deleteCategoryVideo({{ $video->id }})" type="button" class="text-sm text-rose-600 hover:underline">Delete</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No {{ $selectedCategory }} videos uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/60 dark:border-slate-800 shadow p-6 space-y-5">
        <div>
            <h3 class="font-semibold text-lg">Class Recordings</h3>
            <p class="text-sm text-slate-500">Upload recordings (up to 500MB). Video files are auto-compressed when ffmpeg is available on the server.</p>
        </div>

        @if(session('recording_status'))
            <p class="rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 text-sm px-3 py-2">{{ session('recording_status') }}</p>
        @endif

        <form wire:submit="uploadRecording" class="space-y-4" wire:key="recording-upload-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Title</label>
                    <input wire:model.live="recordingTitle" type="text" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm" placeholder="Example: Writing Task 2 Live Class - Week 3">
                    @error('recordingTitle')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recording File</label>
                    <input wire:model="recordingFile" type="file" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm">
                    @error('recordingFile')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Description (Optional)</label>
                    <textarea wire:model.live="recordingDescription" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm" placeholder="Share what this class recording covers..."></textarea>
                </div>
            </div>

            <p wire:loading wire:target="recordingFile" class="text-xs text-indigo-600">Uploading file to temporary storage...</p>

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="recordingFile,uploadRecording"
                    class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="uploadRecording">Upload Recording</span>
                <span wire:loading wire:target="uploadRecording">Processing...</span>
            </button>
        </form>

        <div class="space-y-3">
            <h4 class="font-semibold">My Uploaded Recordings</h4>
            @forelse($recordings as $recording)
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="font-semibold">{{ $recording->title }}</p>
                        <p class="text-xs text-slate-500">Uploaded {{ $recording->created_at->diffForHumans() }}</p>
                        @if($recording->description)
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $recording->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('recordings.play', $recording) }}" target="_blank" class="text-sm text-indigo-600 hover:underline">Open</a>
                        <button wire:click="deleteRecording({{ $recording->id }})" type="button" class="text-sm text-rose-600 hover:underline">Delete</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recordings uploaded yet.</p>
            @endforelse
        </div>
    </section>
</div>
