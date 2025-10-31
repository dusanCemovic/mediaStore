<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostRequestTest extends TestCase
{
    // empty database after start of each test. Then migrates start and so on
    use RefreshDatabase;

    /**
     * This test try to post json without title text
     * @return void
     */
    public function test_upload_fails_when_title_is_missing()
    {
        Storage::fake('public');
        putenv('API_TOKEN=' . env('API_TOKEN'));

        $file = UploadedFile::fake()->image('photo.jpg');

        // send without 'title'
        $response = $this->postJson('/api/upload-media', [
            'description' => 'No title here',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . env('API_TOKEN'),
        ]);

        $response->assertStatus(422); // Unprocessable Content
        $response->assertJsonValidationErrors(['title']);
    }


    /**
     * This test shows that wrong file format is sent
     * @return void
     */
    public function test_upload_fails_with_invalid_file_type()
    {
        Storage::fake('public');
        putenv('API_TOKEN=' . env('API_TOKEN'));

        $file = UploadedFile::fake()->create('document.txt', 10); // Not an image/video

        $response = $this->postJson('/api/upload-media', [
            'title' => 'Invalid File',
            'description' => 'Text file not allowed',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . env('API_TOKEN'),
        ]);

        $response->assertStatus(422); // Unprocessable Content
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * This test try to upload file bigger then our validation limit (77mb)
     * @return void
     */
    public function test_upload_handles_large_file()
    {
        Storage::fake('public');
        putenv('API_TOKEN=' . env('API_TOKEN'));

        $file = UploadedFile::fake()->create('large.mp4', 100 * 1024); // 100 MB

        $response = $this->postJson('/api/upload-media', [
            'title' => 'Big File',
            'description' => 'Testing large upload',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . env('API_TOKEN'),
        ]);

        $response->assertStatus(422);
    }


}
