<?php

namespace Tests\Unit;


use Tests\TestCase;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaServiceTest extends TestCase
{

    use RefreshDatabase;

    /**
     * This is just used to test if MediaService is working well
     * @return void
     */
    public function test_store_proper_file_saved_and_db_inserted()
    {
        // Arrange
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg')->size(512);
        $service = new MediaService();

        // Act
        $media = $service->store($file, 'Test Photo', 'Unit test upload');

        // Assert
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('Test Photo', $media->title);
        $this->assertEquals('Unit test upload', $media->description);

        // File exists in fake disk
        Storage::disk('public')->assertExists($media->path);

        // Record exists in DB
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'filename' => $media->filename,
        ]);
    }

}
