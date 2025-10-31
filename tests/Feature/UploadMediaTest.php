<?php

namespace Tests\Feature;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadMediaTest extends TestCase
{
    // empty database after start of each test. Then migrates start and so on
    use RefreshDatabase;

    /**
     * Test where everything should go smoothly.
     * Upload, file storage, database, response, size same
     * @return void
     */
    public function test_media_upload_saves_file_and_db_record()
    {
        // fake storage so no files are crated
        Storage::fake('public');

        // Put a token in the environment for the middleware to check
        putenv('API_TOKEN=' . env('API_TOKEN'));

        // fake file
        $file = UploadedFile::fake()->image('photo.jpg')->size(1024); // 1MB

        // send post request
        $response = $this->postJson('/api/upload-media', [
            'title' => 'My Photo',
            'description' => 'Test upload',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . env('API_TOKEN'),
        ]);

        // check if we get 201
        $response->assertStatus(201);

        // check response format
        $response->assertJsonStructure(
            [
                'id',
                'title',
                'description',
                'file_type',
                'size',
                'url',
        ]);

        // Assert DB record exists
        $this->assertDatabaseCount('media', 1);

        $media = Media::first();

        // Assert file exists in the fake storage
        Storage::disk('public')->assertExists($media->path);

        // Assert returned size matches DB record
        $this->assertEquals($media->size, $response->json('size'));
    }
}
