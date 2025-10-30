<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(StoreMediaRequest $request): JsonResponse
    {
        // get file
        $file = $request->file('file');

        // Unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in public
        $path = $file->storeAs('media', $filename, 'public');

        // Save in database
        $media = Media::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'filename' => $filename,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
        ]);

        // Public URL (assumes `php artisan storage:link` created)
        $publicUrl = url(Storage::url($path));

        // give response to client
        return response()->json([
            'id' => $media->id,
            'title' => $media->title,
            'description' => $media->description,
            'file_type' => $media->mime,
            'size' => $media->size,
            'url' => $publicUrl,
        ], 201);
    }

    public function list(): JsonResponse
    {
        return response()->json([
            'response'  => 'Success with ping',
        ], 201);
        //return response()->json(Media::all());
    }
}
