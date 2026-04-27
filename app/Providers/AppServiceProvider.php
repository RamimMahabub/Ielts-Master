<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow larger media uploads through Livewire temporary upload endpoint.
        config([
            'livewire.temporary_file_upload.rules' => [
                'required',
                'file',
                'max:512000',
                'mimes:mp3,wav,m4a,aac,ogg,webm,mp4,mov,avi,mkv',
            ],
            'livewire.temporary_file_upload.max_upload_time' => 30,
        ]);
    }
}
