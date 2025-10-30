<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;

class MediaService
{
    public function store(UploadedFile $file, string $title, string $description): Media {

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in the "public/media" directory
        $path = $file->storeAs('media', $filename, 'public');

        // Save in database
        return Media::create([
            'title' => $title,
            'description' => $description,
            'filename' => $filename,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
        ]);
    }
}
