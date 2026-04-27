<?php

namespace App\Livewire\Pages\Admin\MockTest;

use App\Models\MockTest;
use App\Models\MockTestModule;
use App\Models\MockTestModuleItem;
use App\Models\QuestionBankItem;
use App\Support\IeltsTypes;
use App\Support\StudentNotificationDispatcher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public ?int $mockTestId = null;
    public string $title = '';
    public string $testType = 'academic';
    public bool $isPublished = false;

    /** modules: ['listening' => ['duration' => 30, 'item_ids' => [1,2,...]], ...] */
    public array $modules = [];

    public function mount(?int $mockTest = null)
    {
        $defaults = [];
        foreach (IeltsTypes::MODULES as $m) {
            $defaults[$m] = [
                'duration' => IeltsTypes::DEFAULT_DURATIONS[$m],
                'item_ids' => [],
            ];
        }
        $this->modules = $defaults;

        if ($mockTest) {
            $test = MockTest::with('modules.items')->findOrFail($mockTest);
            $this->mockTestId  = $test->id;
            $this->title       = $test->title;
            $this->testType    = $test->test_type;
            $this->isPublished = (bool) $test->is_published;

            foreach ($test->modules as $mod) {
                $this->modules[$mod->module] = [
                    'duration' => $mod->duration_minutes,
                    'item_ids' => $mod->items->pluck('item_id')->toArray(),
                ];
            }
        }
    }

    public function getBankItemsByModuleProperty(): array
    {
        $out = [];
        foreach (IeltsTypes::MODULES as $m) {
            $out[$m] = QuestionBankItem::where('module', $m)
                ->orderBy('title')
                ->get(['id', 'title']);
        }
        return $out;
    }

    public function addItem(string $module, int $itemId): void
    {
        $current = $this->modules[$module]['item_ids'] ?? [];
        if (!in_array($itemId, $current, true)) {
            $current[] = $itemId;
            $this->modules[$module]['item_ids'] = $current;
        }
    }

    public function removeItem(string $module, int $idx): void
    {
        unset($this->modules[$module]['item_ids'][$idx]);
        $this->modules[$module]['item_ids'] = array_values($this->modules[$module]['item_ids']);
    }

    public function moveItem(string $module, int $idx, int $direction): void
    {
        $arr = $this->modules[$module]['item_ids'];
        $newIdx = $idx + $direction;
        if ($newIdx < 0 || $newIdx >= count($arr)) return;
        [$arr[$idx], $arr[$newIdx]] = [$arr[$newIdx], $arr[$idx]];
        $this->modules[$module]['item_ids'] = $arr;
    }

    public function save()
    {
        $this->validate([
            'title'    => 'required|string|max:255',
            'testType' => 'required|in:academic,general',
        ]);

        $wasPublished = false;

        if ($this->mockTestId) {
            $wasPublished = (bool) MockTest::whereKey($this->mockTestId)->value('is_published');
        }

        $test = $this->mockTestId
            ? tap(MockTest::findOrFail($this->mockTestId))->update([
                'title'        => $this->title,
                'test_type'    => $this->testType,
                'is_published' => $this->isPublished,
            ])
            : MockTest::create([
                'title'        => $this->title,
                'test_type'    => $this->testType,
                'is_published' => $this->isPublished,
                'created_by'   => Auth::id(),
            ]);

        $this->mockTestId = $test->id;

        $orderIndex = 0;
        foreach (IeltsTypes::MODULES as $module) {
            $cfg = $this->modules[$module];
            $duration = (int) ($cfg['duration'] ?? IeltsTypes::DEFAULT_DURATIONS[$module]);

            $mod = MockTestModule::updateOrCreate(
                ['mock_test_id' => $test->id, 'module' => $module],
                ['order_index' => $orderIndex++, 'duration_minutes' => $duration]
            );

            // Replace items
            MockTestModuleItem::where('mock_test_module_id', $mod->id)->delete();
            foreach (($cfg['item_ids'] ?? []) as $i => $itemId) {
                MockTestModuleItem::create([
                    'mock_test_module_id' => $mod->id,
                    'item_id'             => (int) $itemId,
                    'order_index'         => $i,
                ]);
            }
        }

        if ($this->isPublished && !$wasPublished) {
            StudentNotificationDispatcher::mockTestPublished($test);
            session()->flash('status', 'Mock test saved, published, and students notified.');
        } else {
            session()->flash('status', 'Mock test saved.');
        }

        return redirect()->route('admin.mock_test.edit', $test);
    }

    public function render()
    {
        return view('livewire.pages.admin.mock-test.edit', [
            'bankItems' => $this->bankItemsByModule,
        ])->layout('layouts.app');
    }
}
