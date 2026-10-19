<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Store a new uploaded file.
     */
    public function store(
        UploadedFile $file,
        string $directory,
        string $disk = 'public'
    ): string {
        return $file->store($directory, $disk);
    }

    /**
     * Replace an existing file with a new uploaded file.
     */
    public function replace(
        ?string $existingPath,
        UploadedFile $newFile,
        string $directory,
        string $disk = 'public'
    ): string {
        if ($existingPath) {
            Storage::disk($disk)->delete($existingPath);
        }

        return $this->store($newFile, $directory, $disk);
    }

    /**
     * Delete an existing uploaded file.
     */
    public function delete(
        ?string $path,
        string $disk = 'public'
    ): bool {
        if (empty($path)) {
            return true;
        }

        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            return true;
        }

        return $storage->delete($path);
    }

    public function delete(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }
}
