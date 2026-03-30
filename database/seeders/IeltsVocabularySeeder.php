<?php

namespace Database\Seeders;

use App\Models\IeltsCollocation;
use App\Models\IeltsQuiz;
use App\Models\IeltsTopic;
use App\Models\IeltsWord;
use Illuminate\Database\Seeder;

class IeltsVocabularySeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Environment',
                'slug' => 'environment',
                'description' => 'High-frequency vocabulary for climate and sustainability tasks.',
                'words' => [
                    ['word' => 'pollution', 'meaning' => 'contamination of the environment', 'sentence' => 'Air pollution is a major concern in urban areas.', 'collocation' => 'reduce pollution'],
                    ['word' => 'biodiversity', 'meaning' => 'variety of plant and animal life', 'sentence' => 'Biodiversity must be protected through stronger laws.', 'collocation' => 'protect biodiversity'],
                    ['word' => 'deforestation', 'meaning' => 'large-scale clearing of forests', 'sentence' => 'Deforestation leads to habitat loss.', 'collocation' => 'prevent deforestation'],
                    ['word' => 'sustainability', 'meaning' => 'long-term environmental balance', 'sentence' => 'Sustainability should guide public policy.', 'collocation' => 'sustainable development'],
                    ['word' => 'emissions', 'meaning' => 'gases released into the atmosphere', 'sentence' => 'Carbon emissions are rising every year.', 'collocation' => 'cut emissions'],
                    ['word' => 'ecosystem', 'meaning' => 'community of organisms and their environment', 'sentence' => 'Coral reef ecosystems are highly fragile.', 'collocation' => 'fragile ecosystem'],
                    ['word' => 'conservation', 'meaning' => 'protection of natural resources', 'sentence' => 'Wildlife conservation requires long-term funding.', 'collocation' => 'wildlife conservation'],
                    ['word' => 'renewable energy', 'meaning' => 'energy from naturally replenished sources', 'sentence' => 'Renewable energy is becoming more affordable.', 'collocation' => 'invest in renewable energy'],
                    ['word' => 'climate change', 'meaning' => 'long-term shifts in weather patterns', 'sentence' => 'Climate change affects food production worldwide.', 'collocation' => 'tackle climate change'],
                    ['word' => 'contamination', 'meaning' => 'making something impure or unsafe', 'sentence' => 'Water contamination can cause serious diseases.', 'collocation' => 'water contamination'],
                    ['word' => 'degradation', 'meaning' => 'decline in quality or condition', 'sentence' => 'Land degradation reduces crop yields.', 'collocation' => 'environmental degradation'],
                    ['word' => 'resource', 'meaning' => 'useful natural material or supply', 'sentence' => 'Governments should conserve natural resources.', 'collocation' => 'conserve resources'],
                ],
                'quizzes' => [
                    ['question' => 'Governments should ___ climate change.', 'options' => ['do', 'make', 'tackle', 'build'], 'answer' => 'tackle', 'word' => 'climate change'],
                    ['question' => 'The best policy is to ___ pollution in cities.', 'options' => ['reduce', 'invent', 'delay', 'hide'], 'answer' => 'reduce', 'word' => 'pollution'],
                    ['question' => 'Solar and wind are forms of ___.', 'options' => ['fossil fuel', 'renewable energy', 'industrial waste', 'deforestation'], 'answer' => 'renewable energy', 'word' => 'renewable energy'],
                ],
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Topic vocabulary for school systems and learning outcomes.',
                'words' => [
                    ['word' => 'curriculum', 'meaning' => 'set of subjects taught in a course', 'sentence' => 'The national curriculum needs regular updates.', 'collocation' => 'national curriculum'],
                    ['word' => 'assessment', 'meaning' => 'evaluation of learning performance', 'sentence' => 'Continuous assessment reduces exam stress.', 'collocation' => 'continuous assessment'],
                    ['word' => 'literacy', 'meaning' => 'ability to read and write', 'sentence' => 'Education programs can improve literacy rates.', 'collocation' => 'improve literacy'],
                    ['word' => 'qualification', 'meaning' => 'official proof of education', 'sentence' => 'A teaching degree is a valuable qualification.', 'collocation' => 'academic qualification'],
                    ['word' => 'discipline', 'meaning' => 'control and orderly behavior', 'sentence' => 'Maintaining discipline supports focused learning.', 'collocation' => 'maintain discipline'],
                    ['word' => 'scholarship', 'meaning' => 'financial aid for study', 'sentence' => 'A scholarship helped her attend university.', 'collocation' => 'win a scholarship'],
                    ['word' => 'syllabus', 'meaning' => 'outline of a course', 'sentence' => 'Students should follow the syllabus carefully.', 'collocation' => 'follow the syllabus'],
                    ['word' => 'critical thinking', 'meaning' => 'ability to analyze and evaluate ideas', 'sentence' => 'Critical thinking is essential in higher education.', 'collocation' => 'develop critical thinking'],
                    ['word' => 'lifelong learning', 'meaning' => 'continuous learning throughout life', 'sentence' => 'Lifelong learning improves career adaptability.', 'collocation' => 'promote lifelong learning'],
                    ['word' => 'academic pressure', 'meaning' => 'stress caused by study demands', 'sentence' => 'Academic pressure can affect mental health.', 'collocation' => 'reduce academic pressure'],
                    ['word' => 'innovation', 'meaning' => 'new methods or ideas', 'sentence' => 'Innovation is transforming classroom teaching.', 'collocation' => 'educational innovation'],
                    ['word' => 'distance learning', 'meaning' => 'education delivered remotely', 'sentence' => 'Distance learning expands access for rural students.', 'collocation' => 'online learning'],
                ],
                'quizzes' => [
                    ['question' => 'Education helps to ___ literacy rates.', 'options' => ['reduce', 'improve', 'damage', 'ignore'], 'answer' => 'improve', 'word' => 'literacy'],
                    ['question' => 'Schools should ___ discipline for better learning.', 'options' => ['maintain', 'erase', 'forget', 'postpone'], 'answer' => 'maintain', 'word' => 'discipline'],
                    ['question' => 'The course structure is called the ___.', 'options' => ['budget', 'curriculum', 'salary', 'industry'], 'answer' => 'curriculum', 'word' => 'curriculum'],
                ],
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Vocabulary for digital transformation and innovation essays.',
                'words' => [
                    ['word' => 'innovation', 'meaning' => 'introduction of new ideas or methods', 'sentence' => 'Technological innovation drives productivity.', 'collocation' => 'technological innovation'],
                    ['word' => 'automation', 'meaning' => 'use of machines to perform tasks', 'sentence' => 'Automation can reduce repetitive work.', 'collocation' => 'industrial automation'],
                    ['word' => 'artificial intelligence', 'meaning' => 'computer systems performing human-like tasks', 'sentence' => 'Artificial intelligence is changing healthcare.', 'collocation' => 'AI development'],
                    ['word' => 'digitalization', 'meaning' => 'conversion to digital systems', 'sentence' => 'Digitalization improves service efficiency.', 'collocation' => 'digital transformation'],
                    ['word' => 'cybersecurity', 'meaning' => 'protection of digital systems from attacks', 'sentence' => 'Cybersecurity is crucial for online banking.', 'collocation' => 'ensure cybersecurity'],
                    ['word' => 'cloud computing', 'meaning' => 'using remote servers for data storage and processing', 'sentence' => 'Cloud computing supports remote collaboration.', 'collocation' => 'cloud services'],
                    ['word' => 'data privacy', 'meaning' => 'protection of personal data', 'sentence' => 'Governments must strengthen data privacy laws.', 'collocation' => 'protect data privacy'],
                    ['word' => 'algorithm', 'meaning' => 'set of steps for solving a problem', 'sentence' => 'The algorithm improved search accuracy.', 'collocation' => 'design an algorithm'],
                    ['word' => 'machine learning', 'meaning' => 'systems learning from data patterns', 'sentence' => 'Machine learning models require quality data.', 'collocation' => 'train a model'],
                    ['word' => 'user interface', 'meaning' => 'visual layout users interact with', 'sentence' => 'A clear user interface improves usability.', 'collocation' => 'improve the interface'],
                    ['word' => 'digital divide', 'meaning' => 'gap in access to technology', 'sentence' => 'Rural communities still face a digital divide.', 'collocation' => 'bridge the digital divide'],
                    ['word' => 'e-commerce', 'meaning' => 'buying and selling online', 'sentence' => 'E-commerce expanded rapidly after the pandemic.', 'collocation' => 'online business'],
                ],
                'quizzes' => [
                    ['question' => 'Technology can ___ productivity.', 'options' => ['increase', 'break', 'destroy', 'ignore'], 'answer' => 'increase', 'word' => 'innovation'],
                    ['question' => 'Protecting systems from hacking is called ___.', 'options' => ['cybersecurity', 'automation', 'globalization', 'urbanization'], 'answer' => 'cybersecurity', 'word' => 'cybersecurity'],
                    ['question' => 'The gap in tech access is the ___.', 'options' => ['digital divide', 'supply chain', 'carbon footprint', 'public policy'], 'answer' => 'digital divide', 'word' => 'digital divide'],
                ],
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'description' => 'Lexis for healthcare, lifestyle, and public health issues.',
                'words' => [
                    ['word' => 'nutrition', 'meaning' => 'process of obtaining healthy food', 'sentence' => 'Good nutrition supports child development.', 'collocation' => 'balanced nutrition'],
                    ['word' => 'obesity', 'meaning' => 'condition of excessive body fat', 'sentence' => 'Obesity rates are increasing in many countries.', 'collocation' => 'combat obesity'],
                    ['word' => 'mental health', 'meaning' => 'emotional and psychological wellbeing', 'sentence' => 'Schools should support students mental health.', 'collocation' => 'improve mental health'],
                    ['word' => 'healthcare', 'meaning' => 'system of medical services', 'sentence' => 'Affordable healthcare should be a public priority.', 'collocation' => 'access healthcare'],
                    ['word' => 'vaccination', 'meaning' => 'administration of a vaccine', 'sentence' => 'Vaccination programs prevent disease outbreaks.', 'collocation' => 'mass vaccination'],
                    ['word' => 'hygiene', 'meaning' => 'practices that maintain health and cleanliness', 'sentence' => 'Poor hygiene increases infection risk.', 'collocation' => 'maintain hygiene'],
                    ['word' => 'epidemic', 'meaning' => 'rapid spread of disease in a region', 'sentence' => 'The epidemic overwhelmed local hospitals.', 'collocation' => 'control an epidemic'],
                    ['word' => 'therapy', 'meaning' => 'treatment to improve a condition', 'sentence' => 'Physical therapy helped her recovery.', 'collocation' => 'receive therapy'],
                    ['word' => 'immunity', 'meaning' => 'ability to resist infection', 'sentence' => 'Regular sleep can strengthen immunity.', 'collocation' => 'boost immunity'],
                    ['word' => 'diagnosis', 'meaning' => 'identification of disease', 'sentence' => 'Early diagnosis saves lives.', 'collocation' => 'accurate diagnosis'],
                    ['word' => 'public health', 'meaning' => 'health of a whole population', 'sentence' => 'Public health campaigns raise awareness.', 'collocation' => 'protect public health'],
                    ['word' => 'life expectancy', 'meaning' => 'average length of life', 'sentence' => 'Life expectancy has improved due to medicine.', 'collocation' => 'increase life expectancy'],
                ],
                'quizzes' => [
                    ['question' => 'Regular exercise helps to ___ fitness.', 'options' => ['damage', 'maintain', 'ignore', 'reduce'], 'answer' => 'maintain', 'word' => 'nutrition'],
                    ['question' => 'An early ___ can improve treatment outcomes.', 'options' => ['salary', 'diagnosis', 'lecture', 'network'], 'answer' => 'diagnosis', 'word' => 'diagnosis'],
                    ['question' => 'Governments should invest in ___.', 'options' => ['public health', 'carbon trading', 'job interviews', 'software bugs'], 'answer' => 'public health', 'word' => 'public health'],
                ],
            ],
            [
                'name' => 'Economy',
                'slug' => 'economy',
                'description' => 'Core economic vocabulary for policy and development discussions.',
                'words' => [
                    ['word' => 'inflation', 'meaning' => 'general rise in prices over time', 'sentence' => 'Inflation reduces purchasing power.', 'collocation' => 'control inflation'],
                    ['word' => 'investment', 'meaning' => 'money committed to gain returns', 'sentence' => 'Foreign investment can create jobs.', 'collocation' => 'attract investment'],
                    ['word' => 'employment', 'meaning' => 'state of having paid work', 'sentence' => 'Employment growth remains a policy target.', 'collocation' => 'create employment'],
                    ['word' => 'unemployment', 'meaning' => 'state of being without a job', 'sentence' => 'Unemployment affects social stability.', 'collocation' => 'reduce unemployment'],
                    ['word' => 'revenue', 'meaning' => 'income generated by government or business', 'sentence' => 'Tourism provides significant tax revenue.', 'collocation' => 'generate revenue'],
                    ['word' => 'budget', 'meaning' => 'planned allocation of money', 'sentence' => 'A realistic budget improves financial control.', 'collocation' => 'manage the budget'],
                    ['word' => 'trade', 'meaning' => 'buying and selling of goods and services', 'sentence' => 'International trade supports economic growth.', 'collocation' => 'international trade'],
                    ['word' => 'recession', 'meaning' => 'period of economic decline', 'sentence' => 'The recession caused many layoffs.', 'collocation' => 'economic recession'],
                    ['word' => 'productivity', 'meaning' => 'amount produced per unit input', 'sentence' => 'Technology can raise productivity levels.', 'collocation' => 'improve productivity'],
                    ['word' => 'consumer demand', 'meaning' => 'desire of buyers for products', 'sentence' => 'Consumer demand rose during the holiday season.', 'collocation' => 'meet demand'],
                    ['word' => 'entrepreneurship', 'meaning' => 'creation and running of new businesses', 'sentence' => 'Entrepreneurship helps diversify the economy.', 'collocation' => 'encourage entrepreneurship'],
                    ['word' => 'financial stability', 'meaning' => 'state of balanced and secure financial system', 'sentence' => 'Strong regulation supports financial stability.', 'collocation' => 'maintain stability'],
                ],
                'quizzes' => [
                    ['question' => 'When prices rise quickly, it is called ___.', 'options' => ['inflation', 'migration', 'pollination', 'innovation'], 'answer' => 'inflation', 'word' => 'inflation'],
                    ['question' => 'Governments should ___ unemployment.', 'options' => ['increase', 'reduce', 'ignore', 'celebrate'], 'answer' => 'reduce', 'word' => 'unemployment'],
                    ['question' => 'New business creation is known as ___.', 'options' => ['recession', 'entrepreneurship', 'automation', 'deforestation'], 'answer' => 'entrepreneurship', 'word' => 'entrepreneurship'],
                ],
            ],
            [
                'name' => 'Work & Employment',
                'slug' => 'work-employment',
                'description' => 'Useful lexis for workplace trends, careers, and labor issues.',
                'words' => [
                    ['word' => 'career', 'meaning' => 'long-term professional journey', 'sentence' => 'She is planning her career in engineering.', 'collocation' => 'build a career'],
                    ['word' => 'salary', 'meaning' => 'fixed regular payment for work', 'sentence' => 'Salary levels vary across industries.', 'collocation' => 'earn a salary'],
                    ['word' => 'promotion', 'meaning' => 'move to a higher position', 'sentence' => 'Strong performance can lead to promotion.', 'collocation' => 'get promoted'],
                    ['word' => 'workload', 'meaning' => 'amount of work assigned', 'sentence' => 'A heavy workload can cause burnout.', 'collocation' => 'heavy workload'],
                    ['word' => 'recruitment', 'meaning' => 'process of hiring employees', 'sentence' => 'Recruitment takes longer for specialist roles.', 'collocation' => 'recruitment process'],
                    ['word' => 'qualification', 'meaning' => 'official proof of ability or training', 'sentence' => 'Many jobs require professional qualifications.', 'collocation' => 'required qualification'],
                    ['word' => 'teamwork', 'meaning' => 'collaborative work by a group', 'sentence' => 'Teamwork improves project outcomes.', 'collocation' => 'promote teamwork'],
                    ['word' => 'leadership', 'meaning' => 'ability to guide and motivate others', 'sentence' => 'Leadership skills are vital for managers.', 'collocation' => 'strong leadership'],
                    ['word' => 'deadline', 'meaning' => 'latest time to complete a task', 'sentence' => 'The team met the deadline successfully.', 'collocation' => 'meet a deadline'],
                    ['word' => 'remote work', 'meaning' => 'working away from a traditional office', 'sentence' => 'Remote work offers more flexibility.', 'collocation' => 'work remotely'],
                    ['word' => 'job security', 'meaning' => 'likelihood of keeping employment', 'sentence' => 'Automation can threaten job security.', 'collocation' => 'ensure job security'],
                    ['word' => 'work-life balance', 'meaning' => 'healthy balance between work and personal life', 'sentence' => 'Companies should support work-life balance.', 'collocation' => 'maintain work-life balance'],
                ],
                'quizzes' => [
                    ['question' => 'Employees feel stressed with a ___ workload.', 'options' => ['heavy', 'silent', 'minor', 'casual'], 'answer' => 'heavy', 'word' => 'workload'],
                    ['question' => 'Good managers demonstrate strong ___.', 'options' => ['pollution', 'leadership', 'recession', 'contamination'], 'answer' => 'leadership', 'word' => 'leadership'],
                    ['question' => 'Remote work can improve ___.', 'options' => ['deforestation', 'work-life balance', 'water contamination', 'traffic fines'], 'answer' => 'work-life balance', 'word' => 'work-life balance'],
                ],
            ],
        ];

        foreach ($topics as $topicData) {
            $topic = IeltsTopic::updateOrCreate(
                ['slug' => $topicData['slug']],
                [
                    'name' => $topicData['name'],
                    'description' => $topicData['description'],
                ]
            );

            $wordMap = [];
            foreach ($topicData['words'] as $wordData) {
                $word = IeltsWord::updateOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'word' => $wordData['word'],
                    ],
                    [
                        'meaning' => $wordData['meaning'],
                        'example_sentence' => $wordData['sentence'],
                    ]
                );

                IeltsCollocation::updateOrCreate(
                    [
                        'word_id' => $word->id,
                        'collocation' => $wordData['collocation'],
                    ],
                    []
                );

                $wordMap[strtolower($wordData['word'])] = $word->id;
            }

            foreach ($topicData['quizzes'] as $quizData) {
                IeltsQuiz::updateOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'question' => $quizData['question'],
                    ],
                    [
                        'word_id' => $wordMap[strtolower($quizData['word'])] ?? null,
                        'quiz_type' => 'mcq',
                        'options_json' => $quizData['options'],
                        'correct_answer' => $quizData['answer'],
                    ]
                );
            }
        }
    }
}
