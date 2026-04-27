<?php

namespace Database\Seeders;

use App\Models\MockTest;
use App\Models\MockTestModule;
use App\Models\MockTestModuleItem;
use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\QuestionGroup;
use App\Models\User;
use App\Support\IeltsTypes;
use Illuminate\Database\Seeder;

/**
 * Builds one full IELTS Academic Mock Test with:
 *  - Listening: 4 sections, 40 questions, 30-min timer (TTS audio fallback per section)
 *  - Reading:   3 passages, 40 questions, 60-min timer
 *  - Writing:   Task 1 + Task 2, 60-min timer
 *  - Speaking:  Parts 1, 2, 3, 14-min timer
 *
 * Auto-graded: Listening + Reading.
 * Manual-graded by instructor: Writing + Speaking (writing_band, speaking_band).
 */
class FullIeltsMockSeeder extends Seeder
{
    protected ?int $adminId = null;

    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->adminId = $admin?->id;

        // Wipe any prior demo content so re-seeding is idempotent
        QuestionBankItem::query()->delete();
        MockTest::query()->delete();

        $listeningItems = $this->seedListening();
        $readingItems   = $this->seedReading();
        $writingItems   = $this->seedWriting();
        $speakingItems  = $this->seedSpeaking();

        $test = MockTest::create([
            'title'        => 'IELTS Academic Mock Test 1',
            'test_type'    => 'academic',
            'is_published' => true,
            'created_by'   => $this->adminId,
        ]);

        $modules = [
            'listening' => $listeningItems,
            'reading'   => $readingItems,
            'writing'   => $writingItems,
            'speaking'  => $speakingItems,
        ];

        $orderIndex = 0;
        foreach ($modules as $module => $items) {
            $mod = MockTestModule::create([
                'mock_test_id'     => $test->id,
                'module'           => $module,
                'order_index'      => $orderIndex++,
                'duration_minutes' => IeltsTypes::DEFAULT_DURATIONS[$module],
            ]);
            foreach ($items as $i => $item) {
                MockTestModuleItem::create([
                    'mock_test_module_id' => $mod->id,
                    'item_id'             => $item->id,
                    'order_index'         => $i,
                ]);
            }
        }
    }

    // ───────────────────────── LISTENING ─────────────────────────

    private function seedListening(): array
    {
        return [
            $this->listeningSection1(),
            $this->listeningSection2(),
            $this->listeningSection3(),
            $this->listeningSection4(),
        ];
    }

    private function listeningSection1(): QuestionBankItem
    {
        $transcript = <<<TXT
You will hear a conversation between Maria, a new customer, and Tom, a receptionist at the Riverside Sports Centre. First you have some time to look at questions one to ten. Now listen carefully and answer questions one to ten.

Tom: Good afternoon, Riverside Sports Centre. How can I help you?
Maria: Hi, I'd like to sign up for a membership, please.
Tom: Of course. Let me take down some details. Could I have your full name?
Maria: Yes, my first name is Maria, and my surname is Lopez. That's L-O-P-E-Z.
Tom: Thank you, Maria. And your address?
Maria: It's 47 Hawthorn Street, in Wellington.
Tom: Hawthorn — H-A-W-T-H-O-R-N — Street. And the postcode?
Maria: NW3 6QR.
Tom: Great. A contact phone number?
Maria: Yes, it's 0-7-7-0-0, then 9-0-0-2-4-4.
Tom: Perfect. Now we have three membership types — gold, silver, and bronze. Gold gives you full access including the swimming pool and all classes. Silver excludes the pool, and bronze covers the gym only.
Maria: I think bronze is best for me. I mainly want the gym.
Tom: Understood — bronze. The monthly fee for bronze is twenty-eight pounds.
Maria: That's fine.
Tom: Would you like to book a class on top of that? We have yoga on Mondays, pilates on Wednesdays, and spinning on Fridays.
Maria: I'd love to try spinning, please.
Tom: Spinning it is. And how did you hear about our centre?
Maria: A friend of mine recommended it.
Tom: A friend — lovely. Last question: when would you like your membership to start?
Maria: Could I start next Monday, please?
Tom: Next Monday — that's perfect. I'll send you a confirmation email shortly.
TXT;

        $item = QuestionBankItem::create([
            'module'     => 'listening',
            'title'      => 'Section 1: Sports Centre Membership Form',
            'transcript' => $transcript,
            'created_by' => $this->adminId,
        ]);

        $g = QuestionGroup::create([
            'item_id'        => $item->id,
            'order_index'    => 0,
            'question_type'  => 'form_completion',
            'instructions'   => 'Complete the form below. Write ONE WORD AND/OR A NUMBER for each answer.',
        ]);

        $qs = [
            ['Surname:', ['Lopez']],
            ['Street name: 47 ____ Street', ['Hawthorn']],
            ['Postcode:', ['NW3 6QR', 'NW36QR']],
            ['Phone number: 07700 ____', ['900244']],
            ['Membership type:', ['bronze']],
            ['Monthly fee: £____', ['28', '28.00']],
            ['Preferred class:', ['spinning']],
            ['How heard about centre: a ____', ['friend']],
            ['Start day:', ['Monday']],
            ['Class day (preferred):', ['Friday']],
        ];
        foreach ($qs as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g->id,
                'q_number'             => $i + 1,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    private function listeningSection2(): QuestionBankItem
    {
        $transcript = <<<TXT
Hello everyone, and welcome to Greenhill Community Centre. My name is Sarah, and I'll be giving you a quick orientation today. The centre opened in nineteen ninety-eight, originally as a small library, but over the years it has grown into the multi-purpose hub you see today.

Let me tell you about our main spaces. As you walk in through reception, on your left you'll see the Wilson Hall — that's our largest room, used mostly for community theatre and big events. On your right is the cafeteria, which is open from eight in the morning until six in the evening. Just past the cafeteria, at the end of the corridor, is the children's library; it has been completely renovated this year with new shelving and a story corner.

Upstairs you'll find three smaller rooms. Room A overlooks the garden and is used for art classes. Room B faces the main road and is best suited to language lessons because it is the quietest. Room C is the largest of the three and contains all our exercise equipment, so that's where fitness classes are held.

Now let me tell you about our weekly programme. We run yoga every Monday morning, from nine until ten thirty. On Tuesdays, we host a cookery workshop in the cafeteria — that's very popular, so please book in advance. Wednesdays are reserved for the children's reading club, which meets from four until five-fifteen. On Thursdays there's a free legal advice session in Wilson Hall, run by volunteers from the local law school. And finally, on Fridays, we have our community choir rehearsal in Room C from seven to nine in the evening.

If you have any questions, please ask at reception. We hope you enjoy your visit.
TXT;

        $item = QuestionBankItem::create([
            'module'     => 'listening',
            'title'      => 'Section 2: Greenhill Community Centre Tour',
            'transcript' => $transcript,
            'created_by' => $this->adminId,
        ]);

        // 11–15: short-answer note completion
        $g1 = QuestionGroup::create([
            'item_id'        => $item->id,
            'order_index'    => 0,
            'question_type'  => 'note_completion',
            'instructions'   => 'Complete the notes below. Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.',
        ]);
        $notes = [
            ['Centre opened in:', ['1998']],
            ['Cafeteria closes at:', ['6 pm', '18:00', 'six', '6']],
            ['Children\'s library was recently:', ['renovated']],
            ['Room used for language lessons:', ['Room B', 'B']],
            ['Room C contains: ____ equipment', ['exercise']],
        ];
        foreach ($notes as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g1->id,
                'q_number'             => 11 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }

        // 16–20: matching activity → day
        $g2 = QuestionGroup::create([
            'item_id'           => $item->id,
            'order_index'       => 1,
            'question_type'     => 'matching',
            'instructions'      => 'Which day is each activity held? Choose A–E.',
            'shared_data_json'  => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        ]);
        $matching = [
            ['Yoga class',          ['A']],
            ['Cookery workshop',    ['B']],
            ['Children\'s reading club', ['C']],
            ['Free legal advice',   ['D']],
            ['Community choir',     ['E']],
        ];
        foreach ($matching as $i => [$prompt, $answers]) {
            $opts = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $j => $k) {
                $opts[] = ['key' => $k, 'text' => ['Monday','Tuesday','Wednesday','Thursday','Friday'][$j]];
            }
            Question::create([
                'group_id'             => $g2->id,
                'q_number'             => 16 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    private function listeningSection3(): QuestionBankItem
    {
        $transcript = <<<TXT
Tutor: So, Andrew and Beth, how is your project on plastic pollution coming along?
Andrew: Quite well, I think. We've narrowed our focus to ocean microplastics rather than plastics in general.
Beth: Yes, otherwise the topic is just too broad.
Tutor: Sensible. Now, what's your main argument?
Andrew: Our central claim is that consumer behaviour, not industrial production, is the bigger driver of microplastic pollution.
Beth: I'm not so sure about that. I think industry is still the more important factor, but consumers play a role.
Tutor: Interesting disagreement. How are you handling that in the report?
Andrew: We present both views and conclude that policy needs to address both, but we lean slightly toward consumer responsibility.
Tutor: And your data sources?
Beth: We've used three: a peer-reviewed paper from twenty twenty-one, government statistics, and a small survey we ran ourselves.
Andrew: The survey was probably the weakest part. Only forty people responded.
Tutor: Forty is quite small, yes. Did you find anything surprising?
Beth: Honestly, the most surprising thing was how unaware most respondents were that synthetic clothing sheds plastic during washing.
Andrew: Almost no one mentioned that.
Tutor: That's a strong finding for the discussion section. What about your recommendations?
Beth: We're suggesting three: a tax on virgin plastic, mandatory filters on washing machines, and a public-awareness campaign.
Andrew: Personally, I think the filters would have the biggest impact, even though Beth thinks the tax would.
Beth: We haven't agreed on that yet.
Tutor: Good — disagreement is fine if your reasoning is clear. How long is the report?
Andrew: We're aiming for four thousand words.
Tutor: That's the upper limit, so be careful with editing. When is your draft due?
Beth: Friday next week.
Tutor: Excellent. Send it to me a day early and I'll give you feedback.
TXT;

        $item = QuestionBankItem::create([
            'module'     => 'listening',
            'title'      => 'Section 3: Student Project Discussion',
            'transcript' => $transcript,
            'created_by' => $this->adminId,
        ]);

        // 21-26: MCQ single
        $g1 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 0,
            'question_type' => 'mcq_single',
            'instructions'  => 'Choose the correct letter, A, B or C.',
        ]);
        $mcq = [
            ['What is the focus of the project?', [
                ['key' => 'A', 'text' => 'plastic pollution in general'],
                ['key' => 'B', 'text' => 'ocean microplastics'],
                ['key' => 'C', 'text' => 'recycling industries'],
            ], 'B'],
            ['What is Andrew\'s central claim?', [
                ['key' => 'A', 'text' => 'Industry causes most microplastic pollution'],
                ['key' => 'B', 'text' => 'Consumer behaviour is the bigger driver'],
                ['key' => 'C', 'text' => 'Government policy is most important'],
            ], 'B'],
            ['Beth disagrees because she believes:', [
                ['key' => 'A', 'text' => 'industry remains the more important factor'],
                ['key' => 'B', 'text' => 'consumers should be punished'],
                ['key' => 'C', 'text' => 'data is unreliable'],
            ], 'A'],
            ['How many people responded to their survey?', [
                ['key' => 'A', 'text' => '14'],
                ['key' => 'B', 'text' => '40'],
                ['key' => 'C', 'text' => '400'],
            ], 'B'],
            ['What did most survey respondents not know?', [
                ['key' => 'A', 'text' => 'that microplastics are dangerous'],
                ['key' => 'B', 'text' => 'that synthetic clothing sheds plastic'],
                ['key' => 'C', 'text' => 'how to recycle correctly'],
            ], 'B'],
            ['Andrew thinks the biggest impact would come from:', [
                ['key' => 'A', 'text' => 'a plastic tax'],
                ['key' => 'B', 'text' => 'washing-machine filters'],
                ['key' => 'C', 'text' => 'a public-awareness campaign'],
            ], 'B'],
        ];
        foreach ($mcq as $i => [$prompt, $opts, $answer]) {
            Question::create([
                'group_id'             => $g1->id,
                'q_number'             => 21 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 27-30: short answer
        $g2 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 1,
            'question_type' => 'short_answer',
            'instructions'  => 'Answer the questions. Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.',
        ]);
        $sa = [
            ['How many data sources did the students use?', ['three', '3']],
            ['What is the maximum word count of the report?', ['4000', 'four thousand']],
            ['When is the draft due?', ['Friday', 'next Friday']],
            ['Which year was the peer-reviewed paper published?', ['2021']],
        ];
        foreach ($sa as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g2->id,
                'q_number'             => 27 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    private function listeningSection4(): QuestionBankItem
    {
        $transcript = <<<TXT
Good morning everyone. Today's lecture is on the history of coffee, and how this familiar drink became one of the world's most traded commodities.

The story begins in the highlands of Ethiopia, where wild coffee plants have grown for thousands of years. According to legend, a goatherd called Kaldi noticed that his goats became unusually energetic after eating the bright red cherries from a particular shrub. Whether or not this is true, it is widely accepted that coffee was first cultivated in the Ethiopian region.

By the fifteenth century, coffee had reached Yemen, where Sufi monks used it to stay awake during long nights of prayer. From the port of Mocha — which gave its name to the drink — coffee spread rapidly across the Arabian Peninsula. The first coffee houses, known as qahveh khaneh, opened in cities such as Mecca and Cairo. They quickly became important centres of conversation, music and chess.

Coffee reached Europe in the seventeenth century. Venetian merchants brought it to Italy, and despite some initial religious suspicion, Pope Clement the Eighth reportedly gave it his blessing after tasting a cup. Coffee houses opened across Europe — first in Venice, then Oxford, then London — and by sixteen seventy-five, England alone had more than three thousand of them.

The drink reached the Americas with the Dutch in sixteen ninety-six, who established the first plantations in colonial Java. Coffee cultivation then expanded enormously in the eighteenth century, particularly in Brazil, which by eighteen-fifty had become the world's largest producer — a position it still holds today.

In the twentieth century, two innovations transformed the industry: instant coffee, patented in nineteen-oh-one, and the invention of the espresso machine in Italy. By the nineteen-eighties, large international chains had begun to standardise coffee preparation around the world.

Today, coffee is the second most traded commodity globally, after crude oil. It supports the livelihoods of around twenty-five million farmers, mostly in developing countries. In the next part of this lecture we will examine the environmental and economic challenges facing the modern coffee industry.
TXT;

        $item = QuestionBankItem::create([
            'module'     => 'listening',
            'title'      => 'Section 4: Lecture on the History of Coffee',
            'transcript' => $transcript,
            'created_by' => $this->adminId,
        ]);

        $g = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 0,
            'question_type' => 'note_completion',
            'instructions'  => 'Complete the notes below. Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.',
        ]);
        $notes = [
            ['Coffee was first cultivated in:', ['Ethiopia']],
            ['Goatherd in legend was called:', ['Kaldi']],
            ['Sufi monks used coffee to stay awake during:', ['prayer', 'prayers']],
            ['Yemeni port that gave its name to the drink:', ['Mocha']],
            ['Pope who reportedly blessed coffee:', ['Clement VIII', 'Clement the Eighth', 'Clement']],
            ['By 1675, England had more than ____ coffee houses', ['3000', 'three thousand']],
            ['Dutch established first plantations in:', ['Java']],
            ['Country that became world\'s largest producer by 1850:', ['Brazil']],
            ['Year instant coffee was patented:', ['1901']],
            ['Approx. number of farmers (millions):', ['25', 'twenty-five']],
        ];
        foreach ($notes as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g->id,
                'q_number'             => 31 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    // ───────────────────────── READING ─────────────────────────

    private function seedReading(): array
    {
        return [
            $this->readingPassage1(),
            $this->readingPassage2(),
            $this->readingPassage3(),
        ];
    }

    private function readingPassage1(): QuestionBankItem
    {
        $passage = <<<HTML
<p><strong>A</strong> The bicycle is one of the most efficient machines ever invented. Most modern bicycles can be traced back to a single design, the so-called "safety bicycle", which appeared in Britain in the eighteen eighties. Yet the road that led to that design was a long and surprising one, full of failed experiments and clever incremental improvements.</p>

<p><strong>B</strong> The earliest recognisable ancestor of the bicycle was the <em>Laufmaschine</em> ("running machine") built by the German inventor Karl von Drais in eighteen seventeen. It had two wheels in line, a handlebar and a wooden frame, but no pedals — riders propelled themselves by pushing their feet against the ground. Although ridiculed at first, the machine quickly became a fashionable curiosity in European cities.</p>

<p><strong>C</strong> The next major step came in the eighteen sixties, when French mechanics fitted pedals directly to the front wheel of a Drais-style frame. The result, nicknamed the "boneshaker" because of its rigid wheels, was uncomfortable but undeniably faster than walking. To gain more speed, builders began enlarging the front wheel, leading to the famous "penny-farthing" of the eighteen seventies. With its tiny back wheel and enormous front, the penny-farthing was elegant, fast, and dangerous; falling from such a height could be fatal.</p>

<p><strong>D</strong> The breakthrough was the safety bicycle of John Kemp Starley, introduced in eighteen eighty-five. By using a chain to drive the rear wheel, Starley made it possible to use two equal-sized wheels and to keep the rider near the ground. The addition of pneumatic tyres, patented by John Boyd Dunlop in eighteen eighty-eight, transformed the ride: rubber filled with air absorbed shocks far better than solid wheels.</p>

<p><strong>E</strong> Within a decade, the bicycle was a mass-market product. It gave ordinary people unprecedented personal mobility and was particularly important for women, who began cycling in great numbers despite social opposition. Some historians argue that the bicycle was as transformative for nineteenth-century women as washing machines would be for twentieth-century housewives.</p>

<p><strong>F</strong> The modern bicycle has changed remarkably little since Starley. Frames are now made of aluminium or carbon fibre rather than steel, and gear systems are more sophisticated, but the basic geometry remains. In an age of electric vehicles and digital transport apps, the simple bicycle continues to outperform almost every alternative on energy per kilometre travelled.</p>
HTML;

        $item = QuestionBankItem::create([
            'module'           => 'reading',
            'title'            => 'Reading Passage 1: The History of the Bicycle',
            'passage_subtitle' => 'You should spend about 20 minutes on Questions 1–13.',
            'passage_html'     => $passage,
            'created_by'       => $this->adminId,
        ]);

        // 1-7: TFNG
        $g1 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 0,
            'question_type' => 'tfng',
            'instructions'  => 'Do the following statements agree with the information in the passage? Write TRUE, FALSE or NOT GIVEN.',
        ]);
        $tfng = [
            ['Most modern bicycles share a common design heritage with the safety bicycle.', 'TRUE'],
            ['The Laufmaschine was an immediate commercial success in Britain.', 'NOT GIVEN'],
            ['Riders of the Laufmaschine used pedals to propel themselves.', 'FALSE'],
            ['The boneshaker was faster than walking.', 'TRUE'],
            ['The penny-farthing was safer than the boneshaker.', 'FALSE'],
            ['John Boyd Dunlop invented the chain drive.', 'FALSE'],
            ['Cycling was widely accepted by society for women from the very beginning.', 'FALSE'],
        ];
        foreach ($tfng as $i => [$prompt, $answer]) {
            $opts = [
                ['key' => 'TRUE', 'text' => 'TRUE'],
                ['key' => 'FALSE', 'text' => 'FALSE'],
                ['key' => 'NOT GIVEN', 'text' => 'NOT GIVEN'],
            ];
            Question::create([
                'group_id'             => $g1->id,
                'q_number'             => 1 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 8-13: sentence completion (short answer)
        $g2 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 1,
            'question_type' => 'sentence_completion',
            'instructions'  => 'Complete the sentences. Write NO MORE THAN TWO WORDS from the passage for each answer.',
        ]);
        $sa = [
            ['The Laufmaschine was built by ____ in 1817.', ['Karl von Drais', 'von Drais', 'Drais']],
            ['The "boneshaker" had pedals attached to the ____ wheel.', ['front']],
            ['The ____ became famous for its enormous front wheel.', ['penny-farthing', 'penny farthing']],
            ['The safety bicycle was introduced by ____ in 1885.', ['John Kemp Starley', 'Starley']],
            ['Pneumatic tyres replaced ____ wheels.', ['solid']],
            ['Modern bike frames are often made of aluminium or ____.', ['carbon fibre', 'carbon']],
        ];
        foreach ($sa as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g2->id,
                'q_number'             => 8 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    private function readingPassage2(): QuestionBankItem
    {
        $passage = <<<HTML
<p><strong>A</strong> Light is the most important environmental signal a plant receives. Plants do not simply grow toward the sun by accident; they sense the direction, quality and duration of light and adjust their development accordingly. The study of these responses, known as <em>photomorphogenesis</em>, has occupied biologists for more than a century.</p>

<p><strong>B</strong> The most familiar response is <em>phototropism</em> — bending toward a light source. In nineteen-twenty-six, the Dutch botanist Frits Went demonstrated that this bending is mediated by a chemical, later named auxin, which migrates to the shaded side of a stem and stimulates the cells there to elongate. The shaded side grows faster, and the stem curves toward the light.</p>

<p><strong>C</strong> But plants do not only respond to whether light is present; they also detect its colour. Two specialised pigments, <em>phytochromes</em> and <em>cryptochromes</em>, allow plants to measure the ratio of red to far-red and the amount of blue light. By comparing these signals, a plant can tell whether it is in open ground or shaded by neighbouring vegetation. Shaded seedlings react by elongating rapidly, in an effort to outgrow their competitors — a behaviour called the "shade-avoidance response".</p>

<p><strong>D</strong> Light also tells the plant when it should flower. Many species flower only when the daily period of darkness exceeds a critical threshold; these are called short-day plants. Others, such as spinach, flower only when nights are short. The pigment phytochrome again plays a central role, acting as a kind of internal clock that links day length with hormonal signals.</p>

<p><strong>E</strong> Recent research has uncovered surprising sophistication in these systems. Modern field experiments show that plants can distinguish their own kin from unrelated neighbours, partly through subtle differences in reflected light. They reduce competition with relatives and increase it with strangers. For agriculture, the implication is significant: planting arrangements that match the plants' light-driven recognition systems may improve yields without using extra fertiliser.</p>
HTML;

        $item = QuestionBankItem::create([
            'module'           => 'reading',
            'title'            => 'Reading Passage 2: How Plants Respond to Light',
            'passage_subtitle' => 'You should spend about 20 minutes on Questions 14–26.',
            'passage_html'     => $passage,
            'created_by'       => $this->adminId,
        ]);

        // 14-18: matching information (paragraph A-E)
        $g1 = QuestionGroup::create([
            'item_id'           => $item->id,
            'order_index'       => 0,
            'question_type'     => 'matching_information',
            'instructions'      => 'Which paragraph contains the following information? Write the correct letter A–E.',
            'shared_data_json'  => ['Paragraph A','Paragraph B','Paragraph C','Paragraph D','Paragraph E'],
        ]);
        $mi = [
            ['the role of auxin in stem bending', 'B'],
            ['plants detecting their relatives', 'E'],
            ['the term used for the broader study of light responses', 'A'],
            ['how shaded plants try to outgrow competitors', 'C'],
            ['plants whose flowering depends on long nights', 'D'],
        ];
        foreach ($mi as $i => [$prompt, $answer]) {
            $opts = [];
            foreach (['A','B','C','D','E'] as $j => $k) {
                $opts[] = ['key' => $k, 'text' => 'Paragraph ' . $k];
            }
            Question::create([
                'group_id'             => $g1->id,
                'q_number'             => 14 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 19-22: MCQ
        $g2 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 1,
            'question_type' => 'mcq_single',
            'instructions'  => 'Choose the correct letter, A, B, C or D.',
        ]);
        $mcq = [
            ['What did Frits Went discover in 1926?', [
                ['key' => 'A', 'text' => 'Plants need water to grow'],
                ['key' => 'B', 'text' => 'Phototropism is mediated by a chemical'],
                ['key' => 'C', 'text' => 'Plants flower only in summer'],
                ['key' => 'D', 'text' => 'Auxin destroys cells'],
            ], 'B'],
            ['According to the passage, phytochromes and cryptochromes:', [
                ['key' => 'A', 'text' => 'are found only in seeds'],
                ['key' => 'B', 'text' => 'measure light colour information'],
                ['key' => 'C', 'text' => 'replace chlorophyll'],
                ['key' => 'D', 'text' => 'are made by humans'],
            ], 'B'],
            ['A "short-day plant" flowers when:', [
                ['key' => 'A', 'text' => 'days are very short'],
                ['key' => 'B', 'text' => 'the dark period passes a threshold'],
                ['key' => 'C', 'text' => 'temperatures fall'],
                ['key' => 'D', 'text' => 'no light is present'],
            ], 'B'],
            ['Recent research suggests planting arrangements based on kin recognition could:', [
                ['key' => 'A', 'text' => 'reduce the need for sunlight'],
                ['key' => 'B', 'text' => 'increase yields without extra fertiliser'],
                ['key' => 'C', 'text' => 'eliminate weeds'],
                ['key' => 'D', 'text' => 'replace genetic engineering'],
            ], 'B'],
        ];
        foreach ($mcq as $i => [$prompt, $opts, $answer]) {
            Question::create([
                'group_id'             => $g2->id,
                'q_number'             => 19 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 23-26: short answer
        $g3 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 2,
            'question_type' => 'short_answer',
            'instructions'  => 'Answer the questions. Write NO MORE THAN TWO WORDS from the passage for each answer.',
        ]);
        $sa = [
            ['What is the broader study of plant light responses called?', ['photomorphogenesis']],
            ['Which chemical migrates to the shaded side of a stem?', ['auxin']],
            ['What name is given to plants\' response to being shaded by neighbours?', ['shade-avoidance response', 'shade avoidance', 'shade-avoidance']],
            ['Which pigment acts as a kind of internal clock for flowering?', ['phytochrome']],
        ];
        foreach ($sa as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g3->id,
                'q_number'             => 23 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    private function readingPassage3(): QuestionBankItem
    {
        $passage = <<<HTML
<p><strong>A</strong> Few technologies have generated as much both excitement and anxiety in modern medicine as artificial intelligence. AI systems — particularly those built on deep neural networks — can now analyse medical images, predict disease, and even draft clinical letters. Yet the question of whether these systems will truly improve healthcare, or merely add complexity, remains hotly debated.</p>

<p><strong>B</strong> The clearest successes have come in image-based diagnostics. Algorithms trained on large datasets of retinal scans, mammograms and skin lesions can now match or exceed expert clinicians at detecting certain conditions. In one widely cited two-thousand-twenty study, an AI model identified breast cancer in mammograms with fewer false positives than radiologists working alone.</p>

<p><strong>C</strong> However, success in narrow image classification does not translate easily into general clinical practice. Real patients have multiple conditions, conflicting symptoms and incomplete records. AI systems trained on data from one hospital often perform poorly when transferred to another, a phenomenon known as <em>distribution shift</em>. Without careful validation, deploying such systems can do more harm than good.</p>

<p><strong>D</strong> A second area of progress is in administrative work. Doctors in many countries spend hours each day writing notes, filling out forms and managing referrals. AI assistants that automatically generate first drafts of these documents can return significant time to clinicians. Some hospitals report that physicians using such tools spend up to forty per cent less time on paperwork.</p>

<p><strong>E</strong> Concerns, however, are real. Bias in training data can lead to systems that work well for some demographic groups but poorly for others. There are also legal and ethical questions: who is responsible if an AI recommendation harms a patient? Most regulatory bodies have only begun to address these issues, and progress is uneven across countries.</p>

<p><strong>F</strong> Looking forward, most experts agree that AI in healthcare will function best as a partner rather than a replacement. Used carefully, it can support doctors with second opinions, reduce paperwork, and help allocate scarce resources. Used carelessly, it risks reinforcing existing inequalities and shifting accountability away from human professionals.</p>
HTML;

        $item = QuestionBankItem::create([
            'module'           => 'reading',
            'title'            => 'Reading Passage 3: Artificial Intelligence in Healthcare',
            'passage_subtitle' => 'You should spend about 20 minutes on Questions 27–40.',
            'passage_html'     => $passage,
            'created_by'       => $this->adminId,
        ]);

        // 27-32: matching headings to paragraphs A-F
        $headings = [
            'i'   => 'A future as a partner, not a replacement',
            'ii'  => 'Why narrow successes do not generalise',
            'iii' => 'Both promise and concern in modern medicine',
            'iv'  => 'Image diagnostics: the strongest success',
            'v'   => 'Time saved through automated paperwork',
            'vi'  => 'Bias, ethics and legal responsibility',
            'vii' => 'A short history of medical computing',
        ];
        $g1 = QuestionGroup::create([
            'item_id'           => $item->id,
            'order_index'       => 0,
            'question_type'     => 'matching_headings',
            'instructions'      => 'Choose the correct heading for each paragraph from the list of headings below.',
            'shared_data_json'  => array_values($headings),
        ]);
        $mh = [
            ['Paragraph A', 'iii'],
            ['Paragraph B', 'iv'],
            ['Paragraph C', 'ii'],
            ['Paragraph D', 'v'],
            ['Paragraph E', 'vi'],
            ['Paragraph F', 'i'],
        ];
        foreach ($mh as $i => [$prompt, $answer]) {
            $opts = [];
            foreach ($headings as $k => $v) {
                $opts[] = ['key' => $k, 'text' => $k . '. ' . $v];
            }
            Question::create([
                'group_id'             => $g1->id,
                'q_number'             => 27 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 33-36: Yes/No/Not Given
        $g2 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 1,
            'question_type' => 'ynng',
            'instructions'  => 'Do the following statements agree with the views of the writer? Write YES, NO or NOT GIVEN.',
        ]);
        $ynng = [
            ['AI image classifiers always perform well in any hospital setting.', 'NO'],
            ['Doctors should be replaced entirely by AI systems for routine diagnosis.', 'NO'],
            ['AI can substantially reduce the time physicians spend on paperwork.', 'YES'],
            ['Most countries have well-developed legal frameworks for medical AI.', 'NO'],
        ];
        foreach ($ynng as $i => [$prompt, $answer]) {
            $opts = [
                ['key' => 'YES', 'text' => 'YES'],
                ['key' => 'NO', 'text' => 'NO'],
                ['key' => 'NOT GIVEN', 'text' => 'NOT GIVEN'],
            ];
            Question::create([
                'group_id'             => $g2->id,
                'q_number'             => 33 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'options_json'         => $opts,
                'correct_answers_json' => [$answer],
                'points'               => 1,
            ]);
        }

        // 37-40: summary completion
        $g3 = QuestionGroup::create([
            'item_id'       => $item->id,
            'order_index'   => 2,
            'question_type' => 'summary_completion',
            'instructions'  => 'Complete the summary. Write NO MORE THAN TWO WORDS from the passage for each answer.',
        ]);
        $sa = [
            ['AI in healthcare has shown the most success in ____ diagnostics.', ['image-based', 'image']],
            ['Models often fail when applied to a new hospital because of ____ shift.', ['distribution']],
            ['Some hospitals report up to ____ percent less time on paperwork.', ['40', 'forty']],
            ['The author concludes AI works best as a ____, not a replacement.', ['partner']],
        ];
        foreach ($sa as $i => [$prompt, $answers]) {
            Question::create([
                'group_id'             => $g3->id,
                'q_number'             => 37 + $i,
                'order_index'          => $i,
                'prompt'               => $prompt,
                'correct_answers_json' => $answers,
                'points'               => 1,
            ]);
        }
        return $item;
    }

    // ───────────────────────── WRITING ─────────────────────────

    private function seedWriting(): array
    {
        $task1 = QuestionBankItem::create([
            'module'       => 'writing',
            'title'        => 'Writing Task 1: Bar Chart — Internet Users by Region (2010 vs 2020)',
            'prompt_html'  => <<<HTML
<p>You should spend about <strong>20 minutes</strong> on this task.</p>
<p>The bar chart below shows the percentage of the population using the internet in five world regions in 2010 and 2020.</p>
<p>Summarise the information by selecting and reporting the main features, and make comparisons where relevant.</p>
<p>Write at least <strong>150 words</strong>.</p>
<table style="border-collapse:collapse;width:100%;max-width:520px;font-size:14px;margin-top:8px">
  <thead><tr style="background:#f1f5f9"><th style="border:1px solid #cbd5e1;padding:6px">Region</th><th style="border:1px solid #cbd5e1;padding:6px">2010 (%)</th><th style="border:1px solid #cbd5e1;padding:6px">2020 (%)</th></tr></thead>
  <tbody>
    <tr><td style="border:1px solid #cbd5e1;padding:6px">North America</td><td style="border:1px solid #cbd5e1;padding:6px">71</td><td style="border:1px solid #cbd5e1;padding:6px">90</td></tr>
    <tr><td style="border:1px solid #cbd5e1;padding:6px">Europe</td><td style="border:1px solid #cbd5e1;padding:6px">63</td><td style="border:1px solid #cbd5e1;padding:6px">87</td></tr>
    <tr><td style="border:1px solid #cbd5e1;padding:6px">Latin America</td><td style="border:1px solid #cbd5e1;padding:6px">35</td><td style="border:1px solid #cbd5e1;padding:6px">72</td></tr>
    <tr><td style="border:1px solid #cbd5e1;padding:6px">Asia &amp; Pacific</td><td style="border:1px solid #cbd5e1;padding:6px">22</td><td style="border:1px solid #cbd5e1;padding:6px">61</td></tr>
    <tr><td style="border:1px solid #cbd5e1;padding:6px">Africa</td><td style="border:1px solid #cbd5e1;padding:6px">10</td><td style="border:1px solid #cbd5e1;padding:6px">40</td></tr>
  </tbody>
</table>
HTML,
            'meta_json'    => ['task_number' => 1, 'min_words' => 150],
            'created_by'   => $this->adminId,
        ]);
        QuestionGroup::create([
            'item_id'       => $task1->id,
            'order_index'   => 0,
            'question_type' => 'essay',
            'instructions'  => 'Write your Task 1 response in the editor on the right.',
        ])->questions()->create([
            'q_number' => 1,
            'order_index' => 0,
            'prompt'   => 'Task 1 response',
            'points'   => 0,
        ]);

        $task2 = QuestionBankItem::create([
            'module'       => 'writing',
            'title'        => 'Writing Task 2: Remote Work and Productivity',
            'prompt_html'  => <<<HTML
<p>You should spend about <strong>40 minutes</strong> on this task.</p>
<p>Write about the following topic:</p>
<blockquote><em>Some people believe that remote working increases productivity, while others argue that working in an office is better for collaboration and overall performance.</em></blockquote>
<p>Discuss both views and give your own opinion.</p>
<p>Give reasons for your answer and include any relevant examples from your own knowledge or experience.</p>
<p>Write at least <strong>250 words</strong>.</p>
HTML,
            'meta_json'    => ['task_number' => 2, 'min_words' => 250],
            'created_by'   => $this->adminId,
        ]);
        QuestionGroup::create([
            'item_id'       => $task2->id,
            'order_index'   => 0,
            'question_type' => 'essay',
            'instructions'  => 'Write your Task 2 response in the editor on the right.',
        ])->questions()->create([
            'q_number' => 1,
            'order_index' => 0,
            'prompt'   => 'Task 2 response',
            'points'   => 0,
        ]);

        return [$task1, $task2];
    }

    // ───────────────────────── SPEAKING ─────────────────────────

    private function seedSpeaking(): array
    {
        // Part 1
        $p1 = QuestionBankItem::create([
            'module'       => 'speaking',
            'title'        => 'Speaking Part 1: Introduction & Familiar Topics',
            'prompt_html'  => '<p>The examiner will introduce themselves and ask you general questions about familiar topics. (4–5 minutes)</p>',
            'meta_json'    => ['part_number' => 1],
            'created_by'   => $this->adminId,
        ]);
        $g1 = QuestionGroup::create([
            'item_id' => $p1->id, 'order_index' => 0, 'question_type' => 'discussion',
            'instructions' => 'Answer each question in 1–2 sentences.',
        ]);
        $p1qs = [
            'Do you work or are you a student?',
            'What do you enjoy most about your hometown?',
            'How often do you read books, and what kind of books do you prefer?',
            'Do you think weekends are long enough? Why or why not?',
        ];
        foreach ($p1qs as $i => $q) {
            Question::create([
                'group_id' => $g1->id, 'q_number' => $i + 1, 'order_index' => $i, 'prompt' => $q, 'points' => 0,
            ]);
        }

        // Part 2
        $p2 = QuestionBankItem::create([
            'module'       => 'speaking',
            'title'        => 'Speaking Part 2: Cue Card — Memorable Journey',
            'prompt_html'  => '<p>You will have <strong>1 minute</strong> to prepare and should speak for <strong>1–2 minutes</strong>.</p>',
            'meta_json'    => [
                'part_number' => 2,
                'cue_card'    => "Describe a memorable journey you have taken.\nYou should say:\n - where you went\n - who you went with\n - what you did during the journey\nand explain why it was memorable for you.",
            ],
            'created_by'   => $this->adminId,
        ]);
        QuestionGroup::create([
            'item_id' => $p2->id, 'order_index' => 0, 'question_type' => 'cue_card',
            'instructions' => 'Speak for 1–2 minutes about the cue card above.',
        ])->questions()->create([
            'q_number' => 1, 'order_index' => 0,
            'prompt' => 'Cue card response (you may take optional notes here).',
            'points' => 0,
        ]);

        // Part 3
        $p3 = QuestionBankItem::create([
            'module'       => 'speaking',
            'title'        => 'Speaking Part 3: Discussion — Travel & Society',
            'prompt_html'  => '<p>The examiner will ask you broader, more abstract questions related to the topic from Part 2. (4–5 minutes)</p>',
            'meta_json'    => ['part_number' => 3],
            'created_by'   => $this->adminId,
        ]);
        $g3 = QuestionGroup::create([
            'item_id' => $p3->id, 'order_index' => 0, 'question_type' => 'discussion',
            'instructions' => 'Give detailed, developed answers.',
        ]);
        $p3qs = [
            'Do you think people travel more now than they did in the past? Why?',
            'What are some of the negative effects of mass tourism on local communities?',
            'Some people say that virtual travel can replace real travel. Do you agree?',
            'How do you think travel will change in the next twenty years?',
        ];
        foreach ($p3qs as $i => $q) {
            Question::create([
                'group_id' => $g3->id, 'q_number' => $i + 1, 'order_index' => $i, 'prompt' => $q, 'points' => 0,
            ]);
        }

        return [$p1, $p2, $p3];
    }
}
