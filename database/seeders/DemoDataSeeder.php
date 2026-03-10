<?php

namespace Database\Seeders;

use App\Models\ContentAsset;
use App\Models\MockTest;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            return;
        }

        $reading = ContentAsset::updateOrCreate(
            ['title' => 'Urban Rooftop Farming in Dense Cities'],
            [
                'type' => 'reading_passage',
                'body_text' => 'Cities worldwide are converting unused rooftop spaces into productive farms. These farms reduce transport emissions, improve local food access, and provide training opportunities for young residents. However, operators must manage soil weight, irrigation design, and seasonal crop planning to sustain long-term yield.',
                'transcript_text' => null,
                'meta_json' => json_encode(['difficulty' => 'medium', 'module' => 'reading']),
            ]
        );

        $listening = ContentAsset::updateOrCreate(
            ['title' => 'Campus Podcast: Improving Study Habits'],
            [
                'type' => 'listening_audio',
                'body_text' => null,
                'file_path' => 'demo/listening-study-habits.mp3',
                'transcript_text' => 'Welcome to Campus Skills Weekly. Today we discuss evidence-based study routines: scheduled review sessions, active recall, and short reflection notes.',
                'meta_json' => json_encode(['difficulty' => 'easy', 'module' => 'listening']),
            ]
        );

        $writing = ContentAsset::updateOrCreate(
            ['title' => 'Writing Task 2: Remote Work and Productivity'],
            [
                'type' => 'writing_task',
                'body_text' => 'Some people believe remote work increases productivity, while others think office-based work is better for collaboration. Discuss both views and give your opinion.',
                'meta_json' => json_encode(['task' => 'task_2', 'module' => 'writing']),
            ]
        );

        $speaking = ContentAsset::updateOrCreate(
            ['title' => 'Speaking Part 2: Describe a skill you learned'],
            [
                'type' => 'speaking_part',
                'body_text' => 'Describe a skill you learned recently. You should say what the skill is, how you learned it, and why it was important to you.',
                'meta_json' => json_encode(['part' => 2, 'module' => 'speaking']),
            ]
        );

        $readingGroup = QuestionGroup::updateOrCreate(
            ['asset_id' => $reading->id, 'question_type' => 'short_answer', 'start_no' => 1, 'end_no' => 2],
            ['instructions' => 'Answer using NO MORE THAN TWO WORDS.']
        );

        $q1 = Question::updateOrCreate(
            ['group_id' => $readingGroup->id, 'q_no' => 1],
            ['prompt' => 'What type of city space is being reused for farming?', 'meta_json' => json_encode([])]
        );
        QuestionAnswer::updateOrCreate(['question_id' => $q1->id], ['answer_text' => 'rooftop spaces', 'explanation' => 'The passage states unused rooftop spaces are converted.']);

        $q2 = Question::updateOrCreate(
            ['group_id' => $readingGroup->id, 'q_no' => 2],
            ['prompt' => 'Name one key technical concern for rooftop farms.', 'meta_json' => json_encode([])]
        );
        QuestionAnswer::updateOrCreate(['question_id' => $q2->id], ['answer_text' => 'soil weight', 'explanation' => 'Operators must consider soil weight.']);

        $listeningGroup = QuestionGroup::updateOrCreate(
            ['asset_id' => $listening->id, 'question_type' => 'note_completion', 'start_no' => 3, 'end_no' => 4],
            ['instructions' => 'Complete the notes with one word only.']
        );

        $q3 = Question::updateOrCreate(
            ['group_id' => $listeningGroup->id, 'q_no' => 3],
            ['prompt' => 'A useful strategy is active _______.', 'meta_json' => json_encode([])]
        );
        QuestionAnswer::updateOrCreate(['question_id' => $q3->id], ['answer_text' => 'recall', 'explanation' => 'The podcast mentions active recall.']);

        $q4 = Question::updateOrCreate(
            ['group_id' => $listeningGroup->id, 'q_no' => 4],
            ['prompt' => 'Learners should write short ______ notes after study sessions.', 'meta_json' => json_encode([])]
        );
        QuestionAnswer::updateOrCreate(['question_id' => $q4->id], ['answer_text' => 'reflection', 'explanation' => 'Reflection notes are recommended.']);

        $writingGroup = QuestionGroup::updateOrCreate(
            ['asset_id' => $writing->id, 'question_type' => 'essay', 'start_no' => 5, 'end_no' => 5],
            ['instructions' => 'Write at least 250 words.']
        );
        Question::updateOrCreate(
            ['group_id' => $writingGroup->id, 'q_no' => 5],
            ['prompt' => 'Discuss both views and give your opinion.', 'meta_json' => json_encode([])]
        );

        $speakingGroup = QuestionGroup::updateOrCreate(
            ['asset_id' => $speaking->id, 'question_type' => 'speaking_response', 'start_no' => 6, 'end_no' => 6],
            ['instructions' => 'Speak for 1-2 minutes.']
        );
        Question::updateOrCreate(
            ['group_id' => $speakingGroup->id, 'q_no' => 6],
            ['prompt' => 'Describe a skill you learned and explain why it matters.', 'meta_json' => json_encode([])]
        );

        $test = MockTest::updateOrCreate(
            ['title' => 'IELTS Original Demo Mock 1'],
            [
                'duration_minutes' => 150,
                'is_published' => true,
                'created_by' => $admin->id,
            ]
        );

        $sections = [
            ['type' => 'reading', 'asset' => $reading, 'index' => 1],
            ['type' => 'listening', 'asset' => $listening, 'index' => 2],
            ['type' => 'writing', 'asset' => $writing, 'index' => 3],
            ['type' => 'speaking', 'asset' => $speaking, 'index' => 4],
        ];

        foreach ($sections as $sectionData) {
            $section = $test->sections()->updateOrCreate(
                ['section_type' => $sectionData['type'], 'order_index' => $sectionData['index']],
                []
            );

            $section->items()->updateOrCreate(
                ['asset_id' => $sectionData['asset']->id],
                ['order_index' => 1]
            );
        }
    }
}
