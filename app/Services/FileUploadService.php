<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Central place for storing (and replacing) uploaded files, used by
 * CategoryController and CourseController for thumbnails/preview videos.
 * Deleting the previous file on replace avoids leaving orphaned uploads
 * behind on every edit — the original controllers didn't do this.
 */
class FileUploadService
{
    public function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    public function replace(?string $existingPath, UploadedFile $newFile, string $directory, string $disk = 'public'): string
    {
        if ($existingPath) {
            Storage::disk($disk)->delete($existingPath);
        }

        return $this->store($newFile, $directory, $disk);
    }
}
