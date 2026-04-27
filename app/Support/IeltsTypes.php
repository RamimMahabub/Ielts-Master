<?php

namespace App\Support;

class IeltsTypes
{
    public const MODULES = ['listening', 'reading', 'writing', 'speaking'];

    public const DEFAULT_DURATIONS = [
        'listening' => 30,
        'reading'   => 60,
        'writing'   => 60,
        'speaking'  => 14,
    ];

    /** Question types valid per module (label => key). */
    public static function questionTypes(string $module): array
    {
        return match ($module) {
            'listening' => [
                'mcq_single'             => 'Multiple Choice (one answer)',
                'mcq_multi'              => 'Multiple Choice (multiple answers)',
                'matching'               => 'Matching',
                'plan_map_diagram'       => 'Plan / Map / Diagram Labelling',
                'form_completion'        => 'Form Completion',
                'note_completion'        => 'Note Completion',
                'table_completion'       => 'Table Completion',
                'flow_chart_completion'  => 'Flow-chart Completion',
                'summary_completion'     => 'Summary Completion',
                'sentence_completion'    => 'Sentence Completion',
                'short_answer'           => 'Short-answer Questions',
            ],
            'reading' => [
                'mcq_single'                 => 'Multiple Choice (one answer)',
                'mcq_multi'                  => 'Multiple Choice (multiple answers)',
                'tfng'                       => 'True / False / Not Given',
                'ynng'                       => 'Yes / No / Not Given',
                'matching_headings'          => 'Matching Headings',
                'matching_information'       => 'Matching Information',
                'matching_features'          => 'Matching Features',
                'matching_sentence_endings'  => 'Matching Sentence Endings',
                'sentence_completion'        => 'Sentence Completion',
                'summary_completion'         => 'Summary Completion',
                'note_completion'            => 'Note Completion',
                'table_completion'           => 'Table Completion',
                'flow_chart_completion'      => 'Flow-chart Completion',
                'diagram_label_completion'   => 'Diagram Label Completion',
                'short_answer'               => 'Short-answer Questions',
            ],
            'writing' => [
                'essay' => 'Essay / Task Response',
            ],
            'speaking' => [
                'cue_card'   => 'Cue Card (Part 2)',
                'discussion' => 'Discussion Questions (Part 1 / 3)',
            ],
            default => [],
        };
    }

    /** Whether questions of this type are auto-gradable. */
    public static function isAutoGradable(string $type): bool
    {
        return in_array($type, [
            'mcq_single', 'mcq_multi', 'tfng', 'ynng',
            'matching', 'matching_headings', 'matching_information',
            'matching_features', 'matching_sentence_endings',
            'plan_map_diagram',
            'form_completion', 'note_completion', 'table_completion',
            'flow_chart_completion', 'summary_completion', 'sentence_completion',
            'diagram_label_completion', 'short_answer',
        ], true);
    }

    /** Whether the type uses fixed options (single-select). */
    public static function hasOptions(string $type): bool
    {
        return in_array($type, [
            'mcq_single', 'mcq_multi', 'tfng', 'ynng',
            'matching', 'matching_headings', 'matching_information',
            'matching_features', 'matching_sentence_endings',
        ], true);
    }
}
