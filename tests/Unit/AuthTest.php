<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // empty database after start of each test. Then migrates start and so on
    use RefreshDatabase;

    public string $token = 'test';
    public string $wrongToken = 'test-wrong-token';

    /**
     * This test is used when token is invalid
     * @return void
     */
    public function test_upload_fails_with_invalid_token()
    {
        Storage::fake('public');
        // Put a token in the environment for the middleware to check
        putenv('API_TOKEN=' . $this->wrongToken);

        $file = UploadedFile::fake()->image('photo.jpg');

        // Wrong token sent
        $response = $this->postJson('/api/upload-media', [
            'title' => 'Bad Token',
            'description' => 'Unauthorized attempt',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . $this->wrongToken,
        ]);

        $response->assertStatus(401); // Unauthorized status
        $response->assertJson(['message' => 'Unauthorized']);
    }

    /**
     * This test is used when no token is used
     * @return void
     */
    public function test_request_without_token_is_rejected()
    {
        $response = $this->postJson('/api/upload-media', [
            'title' => 'No Token',
            'description' => 'This should fail',
        ]);

        $response->assertStatus(401); // Unauthorized status
        $response->assertJson(['message' => 'Unauthorized']);
    }

}
