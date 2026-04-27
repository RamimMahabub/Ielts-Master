@php
    use App\Support\IeltsTypes;
    // Group consecutive flat questions by their question_group id so we can render shared instructions/options once.
    $byGroup = [];
    foreach ($flatQuestions as $row) {
        $gid = $row['group']->id;
        if (!isset($byGroup[$gid])) {
            $byGroup[$gid] = ['group' => $row['group'], 'rows' => []];
        }
        $byGroup[$gid]['rows'][] = $row;
    }
@endphp

<div class="space-y-6">
    @foreach($byGroup as $gid => $bundle)
        @php
            $g = $bundle['group'];
            $type = $g->question_type;
            $shared = $g->shared_data_json ?? [];
            $hasOptions = IeltsTypes::hasOptions($type);
        @endphp
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
            @if($g->instructions)
                <p class="text-sm italic text-slate-600 dark:text-slate-400 mb-3">{{ $g->instructions }}</p>
            @endif

            @if(!empty($shared) && in_array($type, ['matching_headings', 'matching_information', 'matching_features', 'matching_sentence_endings', 'matching']))
                <div class="rounded-lg bg-slate-50 dark:bg-slate-800/60 p-3 mb-4 text-sm">
                    <strong class="block mb-1">Options:</strong>
                    <ul class="space-y-1">
                        @foreach($shared as $idx => $opt)
                            <li><span class="font-semibold mr-2">{{ chr(65 + $idx) }}.</span>{{ $opt }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-3">
                @foreach($bundle['rows'] as $row)
                    @php
                        $q = $row['q'];
                        $qid = $q->id;
                        $val = $answers[$qid] ?? null;
                    @endphp
                    <div class="flex gap-3">
                        <span class="font-bold text-indigo-600 w-6 shrink-0">{{ $q->q_number }}.</span>
                        <div class="flex-1 space-y-2">
                            @if($q->prompt)
                                <p class="text-sm">{{ $q->prompt }}</p>
                            @endif

                            @if($type === 'mcq_multi')
                                @foreach($q->options_json ?? [] as $opt)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" value="{{ $opt['key'] }}"
                                               @checked(is_array($val) && in_array($opt['key'], $val))
                                               onchange="(() => {
                                                   const wid = this.closest('[wire\\:id]').getAttribute('wire:id');
                                                   const cb = this;
                                                   const all = Array.from(cb.closest('div').parentElement.querySelectorAll('input[type=checkbox]')).filter(i=>i.checked).map(i=>i.value);
                                                   Livewire.find(wid).call('saveAnswer', {{ $qid }}, all);
                                               })()">
                                        <span class="font-semibold">{{ $opt['key'] }}.</span> {{ $opt['text'] }}
                                    </label>
                                @endforeach
                            @elseif($hasOptions)
                                @foreach($q->options_json ?? [] as $opt)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="radio" name="q-{{ $qid }}" value="{{ $opt['key'] }}"
                                               @checked($val === $opt['key'])
                                               wire:change="saveAnswer({{ $qid }}, '{{ $opt['key'] }}')">
                                        <span class="font-semibold">{{ $opt['key'] }}.</span> {{ $opt['text'] }}
                                    </label>
                                @endforeach
                            @else
                                <input type="text" value="{{ is_string($val) ? $val : '' }}"
                                       wire:change="saveAnswer({{ $qid }}, $event.target.value)"
                                       class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
                                       placeholder="Your answer">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
