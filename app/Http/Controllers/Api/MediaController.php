<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(StoreMediaRequest $request, MediaService $service): JsonResponse
    {
        // get file
        $file = $request->file('file');

        // this is service for saving in folder and storing file in db
        $media = $service->store(
            $file,
            $request->input('title'),
            $request->input('description')
        );

        // Public URL
        $publicUrl = url(Storage::url($media->path));

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

    public function ping(): JsonResponse
    {
        return response()->json([
            'response'  => 'Success with ping',
        ], 201);
    }

    public function list(): JsonResponse {

        $all = Media::all();
        $result = [];

        foreach ($all as $media) {
            $result[] = [
                'id' => $media->id,
                'title' => $media->title,
                'url' => url(Storage::url($media->path)),
            ];
        }

        return response()->json($result, 201);
    }
}
