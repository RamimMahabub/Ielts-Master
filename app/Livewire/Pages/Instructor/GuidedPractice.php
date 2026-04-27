<?php

namespace App\Livewire\Pages\Instructor;

use App\Models\ClassRecording;
use App\Models\GuidedPracticeVideo;
use App\Models\LearningResource;
use App\Support\IeltsTypes;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\Process\Process;

class GuidedPractice extends Component
{
    use WithFileUploads;

    public string $selectedCategory = 'listening';
    public array $resourceInputs = [''];

    public string $recordingTitle = '';
    public ?string $recordingDescription = null;
    public $recordingFile;

    public string $categoryVideoTitle = '';
    public ?string $categoryVideoDescription = null;
    public $categoryVideoFile;

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(!$user || (!$user->hasRole('admin') && $user->instructor_status !== 'approved'), 403);
        $this->loadResourceInputs();
    }

    public function setCategory(string $category): void
    {
        if (!in_array($category, IeltsTypes::MODULES, true)) {
            return;
        }

        $this->selectedCategory = $category;
        $this->loadResourceInputs();
    }

    public function addResourceInput(): void
    {
        $this->resourceInputs[] = '';
    }

    public function removeResourceInput(int $index): void
    {
        if (!isset($this->resourceInputs[$index])) {
            return;
        }

        unset($this->resourceInputs[$index]);
        $this->resourceInputs = array_values($this->resourceInputs);

        if (count($this->resourceInputs) === 0) {
            $this->resourceInputs = [''];
        }
    }

    public function saveResources(): void
    {
        $this->validate([
            'selectedCategory' => 'required|in:listening,reading,writing,speaking',
            'resourceInputs' => 'required|array|min:1',
            'resourceInputs.*' => 'nullable|string|max:12000',
        ]);

        $prepared = collect($this->resourceInputs)
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->values();

        LearningResource::where('created_by', Auth::id())
            ->where('category', $this->selectedCategory)
            ->delete();

        foreach ($prepared as $index => $content) {
            LearningResource::create([
                'category' => $this->selectedCategory,
                'content' => $content,
                'order_index' => $index,
                'created_by' => Auth::id(),
            ]);
        }

        $this->resourceInputs = $prepared->isEmpty() ? [''] : $prepared->all();

        session()->flash('status', 'Resources updated for ' . ucfirst($this->selectedCategory) . '.');
    }

    public function uploadRecording(): void
    {
        $this->resetValidation(['recordingTitle', 'recordingFile']);

        $this->validate([
            'recordingTitle' => 'required|string|max:255',
            'recordingDescription' => 'nullable|string|max:2000',
            'recordingFile' => 'required|file|mimes:mp3,wav,m4a,aac,ogg,webm,mp4,mov,avi,mkv|max:512000',
        ]);

        $path = $this->recordingFile->store('class-recordings', 'public');
        $compressed = false;

        if ($this->isVideoUpload($this->recordingFile) && $this->isFfmpegAvailable()) {
            $compressedPath = $this->compressUploadedVideo($this->recordingFile, 'class-recordings');
            if ($compressedPath) {
                Storage::disk('public')->delete($path);
                $path = $compressedPath;
                $compressed = true;
            }
        }

        ClassRecording::create([
            'title' => $this->recordingTitle,
            'description' => $this->recordingDescription,
            'recording_path' => $path,
            'created_by' => Auth::id(),
        ]);

        $this->recordingTitle = '';
        $this->recordingDescription = null;
        $this->recordingFile = null;

        session()->flash('recording_status', $compressed
            ? 'Class recording uploaded and compressed successfully.'
            : 'Class recording uploaded successfully.');
    }

    public function uploadCategoryVideo(): void
    {
        $this->resetValidation(['categoryVideoTitle', 'categoryVideoFile']);

        $this->validate([
            'selectedCategory' => 'required|in:listening,reading,writing,speaking',
            'categoryVideoTitle' => 'required|string|max:255',
            'categoryVideoDescription' => 'nullable|string|max:2000',
            'categoryVideoFile' => 'required|file|mimes:mp4,mov,avi,mkv,webm|max:512000',
        ]);

        $path = $this->categoryVideoFile->store('guided-category-videos', 'public');
        $compressed = false;

        if ($this->isVideoUpload($this->categoryVideoFile) && $this->isFfmpegAvailable()) {
            $compressedPath = $this->compressUploadedVideo($this->categoryVideoFile, 'guided-category-videos');
            if ($compressedPath) {
                Storage::disk('public')->delete($path);
                $path = $compressedPath;
                $compressed = true;
            }
        }

        GuidedPracticeVideo::create([
            'category' => $this->selectedCategory,
            'title' => $this->categoryVideoTitle,
            'description' => $this->categoryVideoDescription,
            'video_path' => $path,
            'created_by' => Auth::id(),
        ]);

        $this->categoryVideoTitle = '';
        $this->categoryVideoDescription = null;
        $this->categoryVideoFile = null;

        session()->flash('category_video_status', $compressed
            ? ucfirst($this->selectedCategory) . ' video uploaded and compressed successfully.'
            : ucfirst($this->selectedCategory) . ' video uploaded successfully.');
    }

    public function updatedRecordingTitle(): void
    {
        $this->resetValidation('recordingTitle');
    }

    public function updatedRecordingFile(): void
    {
        $this->resetValidation('recordingFile');
    }

    public function updatedCategoryVideoTitle(): void
    {
        $this->resetValidation('categoryVideoTitle');
    }

    public function updatedCategoryVideoFile(): void
    {
        $this->resetValidation('categoryVideoFile');
    }

    protected function isVideoUpload($uploadedFile): bool
    {
        $mime = (string) $uploadedFile?->getMimeType();
        return Str::startsWith($mime, 'video/');
    }

    protected function isFfmpegAvailable(): bool
    {
        $process = new Process(['ffmpeg', '-version']);
        $process->setTimeout(5);
        $process->run();

        return $process->isSuccessful();
    }

    protected function compressUploadedVideo($uploadedFile, string $targetDirectory): ?string
    {
        $source = $uploadedFile?->getRealPath();
        if (!$source) {
            return null;
        }

        $tempOutput = tempnam(sys_get_temp_dir(), 'gp-video-');
        if ($tempOutput === false) {
            return null;
        }

        $outputMp4 = $tempOutput . '.mp4';

        $process = new Process([
            'ffmpeg',
            '-y',
            '-i',
            $source,
            '-vcodec',
            'libx264',
            '-preset',
            'veryfast',
            '-crf',
            '28',
            '-acodec',
            'aac',
            '-b:a',
            '128k',
            $outputMp4,
        ]);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputMp4)) {
            @unlink($tempOutput);
            @unlink($outputMp4);
            return null;
        }

        $finalPath = $targetDirectory . '/' . Str::uuid() . '.mp4';
        Storage::disk('public')->putFileAs(
            $targetDirectory,
            new File($outputMp4),
            basename($finalPath)
        );

        @unlink($tempOutput);
        @unlink($outputMp4);

        return $finalPath;
    }

    public function deleteRecording(int $recordingId): void
    {
        $recording = ClassRecording::where('created_by', Auth::id())->findOrFail($recordingId);
        $recording->delete();

        session()->flash('recording_status', 'Class recording deleted.');
    }

    public function deleteCategoryVideo(int $videoId): void
    {
        $video = GuidedPracticeVideo::where('created_by', Auth::id())->findOrFail($videoId);
        $video->delete();

        session()->flash('category_video_status', ucfirst($this->selectedCategory) . ' video deleted.');
    }

    protected function loadResourceInputs(): void
    {
        $items = LearningResource::where('created_by', Auth::id())
            ->where('category', $this->selectedCategory)
            ->orderBy('order_index')
            ->pluck('content')
            ->toArray();

        $this->resourceInputs = count($items) > 0 ? $items : [''];
    }

    public function render()
    {
        $recordings = ClassRecording::where('created_by', Auth::id())
            ->latest()
            ->get();

        $categoryVideos = GuidedPracticeVideo::where('created_by', Auth::id())
            ->where('category', $this->selectedCategory)
            ->latest()
            ->get();

        return view('livewire.pages.instructor.guided-practice', [
            'categories' => IeltsTypes::MODULES,
            'recordings' => $recordings,
            'categoryVideos' => $categoryVideos,
        ])->layout('layouts.app');
    }
}
