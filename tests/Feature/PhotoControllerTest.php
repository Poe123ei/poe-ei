<?php

/**
 * NOT PART OF THE DORM PORTAL.
 *
 * Leftover from an earlier photo-gallery exercise. Delete this file together
 * with the rest of the photo feature in Milestone 1, Task 1.1.
 */

use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

dataset('invalid photo titles', [
    'missing' => [null, 'The title field is required.'],
    'not text' => [['unexpected'], 'The title field must be a string.'],
    'too long' => [str_repeat('a', 256), 'The title field must not be greater than 255 characters.'],
]);

dataset('invalid photo images', [
    'text file' => [fn (): UploadedFile => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'), 'The image field must be an image.'],
    'SVG' => [fn (): UploadedFile => UploadedFile::fake()->createWithContent('drawing.svg', '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>'), 'The image field must be an image.'],
    'over two megabytes' => [fn (): UploadedFile => UploadedFile::fake()->image('room.jpg')->size(2049), 'The image field must not be greater than 2048 kilobytes.'],
]);

describe('browsing photos', function (): void {
    test('renders an empty gallery', function (): void {
        $response = $this->get(route('photos.index'));

        $response->assertOk()->assertViewIs('photos.index');
        expect($response['photos'])->toHaveCount(0);
    });

    test('shows twelve newest photos and escapes their titles', function (): void {
        $oldestPhoto = Photo::factory()->create(['title' => 'Oldest photo']);
        Photo::factory()->count(11)->create();
        $newestPhoto = Photo::factory()->create(['title' => '<script>alert("photo")</script>']);

        $response = $this->get(route('photos.index'));

        $response->assertOk()->assertSee($newestPhoto->title)
            ->assertDontSee($newestPhoto->title, false)->assertDontSee($oldestPhoto->title);
        expect($response['photos'])->toHaveCount(12);
        expect($response['photos']->total())->toBe(13);
        expect($response['photos']->first()->id)->toBe($newestPhoto->id);
    });

    test('shows the remaining photo on the next page', function (): void {
        $oldestPhoto = Photo::factory()->create(['title' => 'Oldest photo']);
        Photo::factory()->count(12)->create();

        $response = $this->get(route('photos.index', ['page' => 2]));

        $response->assertOk()->assertSee($oldestPhoto->title);
        expect($response['photos'])->toHaveCount(1);
    });

    test('renders a multipart upload form', function (): void {
        $response = $this->get(route('photos.create'));

        $response->assertOk()->assertViewIs('photos.create')
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="title"', false)->assertSee('name="image"', false);
    });

    test('renders the selected photo with an escaped title', function (string $routeName, string $viewName): void {
        $photo = Photo::factory()->create(['title' => '<script>alert("photo")</script>']);

        $response = $this->get(route($routeName, $photo));

        $response->assertOk()->assertViewIs($viewName)
            ->assertViewHas('photo', fn (Photo $shownPhoto): bool => $shownPhoto->is($photo))
            ->assertSee($photo->title)->assertDontSee($photo->title, false)
            ->assertSee(Storage::disk('public')->url($photo->path));
    })->with([
        'detail page' => ['photos.show', 'photos.show'],
        'edit form' => ['photos.edit', 'photos.edit'],
    ]);
});

describe('uploading photos', function (): void {
    test('stores an image at its generated path and accepts the title and size limits', function (): void {
        Storage::fake('public');
        $title = str_repeat('a', 255);
        $image = UploadedFile::fake()->image('room.jpg')->size(2048);
        $expectedPath = 'photos/'.$image->hashName();

        $response = $this->post(route('photos.store'), [
            'title' => $title, 'image' => $image, 'path' => 'photos/untrusted.jpg',
        ]);

        $response->assertRedirectToRoute('photos.show', Photo::query()->sole())
            ->assertSessionHas('status', 'Photo uploaded successfully.');
        $this->assertDatabaseHas('photos', ['title' => $title, 'path' => $expectedPath]);
        Storage::disk('public')->assertExists($expectedPath);
    });

    test('requires both a title and an image', function (): void {
        Storage::fake('public');

        $response = $this->from(route('photos.create'))->post(route('photos.store'));

        $response->assertRedirectToRoute('photos.create')->assertSessionHasErrors([
            'title' => 'The title field is required.', 'image' => 'The image field is required.',
        ]);
        $this->assertDatabaseCount('photos', 0);
        Storage::disk('public')->assertDirectoryEmpty('photos');
    });

    test('rejects an invalid title before storing the image', function (mixed $title, string $message): void {
        Storage::fake('public');

        $response = $this->from(route('photos.create'))->post(route('photos.store'), [
            'title' => $title, 'image' => UploadedFile::fake()->image('room.jpg'),
        ]);

        $response->assertRedirectToRoute('photos.create')->assertSessionHasErrors(['title' => $message]);
        $this->assertDatabaseCount('photos', 0);
        Storage::disk('public')->assertDirectoryEmpty('photos');
    })->with('invalid photo titles');

    test('rejects an invalid image without creating a photo', function (UploadedFile $image, string $message): void {
        Storage::fake('public');

        $response = $this->from(route('photos.create'))->post(route('photos.store'), ['title' => 'Room', 'image' => $image]);

        $response->assertRedirectToRoute('photos.create')->assertSessionHasErrors(['image' => $message]);
        $this->assertDatabaseCount('photos', 0);
        Storage::disk('public')->assertDirectoryEmpty('photos');
    })->with('invalid photo images');

    test('removes the uploaded image when saving fails', function (): void {
        Storage::fake('public');
        Exceptions::fake();
        Photo::creating(function (): void {
            throw new RuntimeException('Unable to save the photo.');
        });

        try {
            $response = $this->post(route('photos.store'), ['title' => 'Room', 'image' => UploadedFile::fake()->image('room.jpg')]);

            $response->assertServerError();
            $this->assertDatabaseCount('photos', 0);
            Storage::disk('public')->assertDirectoryEmpty('photos');
            Exceptions::assertReported(fn (RuntimeException $exception): bool => $exception->getMessage() === 'Unable to save the photo.');
        } finally {
            Event::forget('eloquent.creating: '.Photo::class);
        }
    });
});

describe('updating photos', function (): void {
    test('changes the title while retaining the existing image', function (array $optionalImage): void {
        Storage::fake('public');
        $photo = Photo::factory()->create(['title' => 'Original title']);
        Storage::disk('public')->put($photo->path, 'original image');

        $response = $this->put(route('photos.update', $photo), [
            'title' => 'Updated title', 'path' => 'photos/untrusted.jpg', ...$optionalImage,
        ]);

        $response->assertRedirectToRoute('photos.show', $photo)->assertSessionHas('status', 'Photo updated successfully.');
        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'title' => 'Updated title', 'path' => $photo->path]);
        Storage::disk('public')->assertExists($photo->path, 'original image');
    })->with(['image omitted' => [[]], 'image is null' => [['image' => null]]]);

    test('replaces the image and removes the previous file', function (): void {
        Storage::fake('public');
        $photo = Photo::factory()->create();
        Storage::disk('public')->put($photo->path, 'original image');
        $image = UploadedFile::fake()->image('replacement.png');
        $expectedPath = 'photos/'.$image->hashName();

        $response = $this->put(route('photos.update', $photo), ['title' => 'Updated title', 'image' => $image]);

        $response->assertRedirectToRoute('photos.show', $photo);
        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'title' => 'Updated title', 'path' => $expectedPath]);
        Storage::disk('public')->assertExists($expectedPath)->assertMissing($photo->path);
    });

    test('preserves the original photo when its new title is invalid', function (mixed $title, string $message): void {
        Storage::fake('public');
        $photo = Photo::factory()->create(['title' => 'Original title']);
        Storage::disk('public')->put($photo->path, 'original image');
        $image = UploadedFile::fake()->image('replacement.jpg');

        $response = $this->from(route('photos.edit', $photo))->put(route('photos.update', $photo), ['title' => $title, 'image' => $image]);

        $response->assertRedirectToRoute('photos.edit', $photo)->assertSessionHasErrors(['title' => $message]);
        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'title' => 'Original title', 'path' => $photo->path]);
        Storage::disk('public')->assertExists($photo->path, 'original image')->assertMissing('photos/'.$image->hashName());
    })->with('invalid photo titles');

    test('preserves the original photo when its replacement image is invalid', function (UploadedFile $image, string $message): void {
        Storage::fake('public');
        $photo = Photo::factory()->create(['title' => 'Original title']);
        Storage::disk('public')->put($photo->path, 'original image');

        $response = $this->from(route('photos.edit', $photo))->put(route('photos.update', $photo), ['title' => 'Updated title', 'image' => $image]);

        $response->assertRedirectToRoute('photos.edit', $photo)->assertSessionHasErrors(['image' => $message]);
        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'title' => 'Original title', 'path' => $photo->path]);
        Storage::disk('public')->assertExists($photo->path, 'original image')->assertMissing('photos/'.$image->hashName());
    })->with('invalid photo images');

    test('keeps the original image and removes the replacement when saving fails', function (): void {
        Storage::fake('public');
        $photo = Photo::factory()->create(['title' => 'Original title']);
        Storage::disk('public')->put($photo->path, 'original image');
        $image = UploadedFile::fake()->image('replacement.jpg');
        Exceptions::fake();
        Photo::updating(function (): void {
            throw new RuntimeException('Unable to update the photo.');
        });

        try {
            $response = $this->put(route('photos.update', $photo), ['title' => 'Updated title', 'image' => $image]);

            $response->assertServerError();
            $this->assertDatabaseHas('photos', ['id' => $photo->id, 'title' => 'Original title', 'path' => $photo->path]);
            Storage::disk('public')->assertExists($photo->path, 'original image')->assertMissing('photos/'.$image->hashName());
            Exceptions::assertReported(fn (RuntimeException $exception): bool => $exception->getMessage() === 'Unable to update the photo.');
        } finally {
            Event::forget('eloquent.updating: '.Photo::class);
        }
    });
});

test('deletes the selected photo and file while retaining other photos', function (): void {
    Storage::fake('public');
    $photo = Photo::factory()->create();
    $otherPhoto = Photo::factory()->create();
    Storage::disk('public')->put($photo->path, 'selected image');
    Storage::disk('public')->put($otherPhoto->path, 'other image');

    $response = $this->delete(route('photos.destroy', $photo));

    $response->assertRedirectToRoute('photos.index')->assertSessionHas('status', 'Photo deleted successfully.');
    $this->assertModelMissing($photo);
    $this->assertModelExists($otherPhoto);
    Storage::disk('public')->assertMissing($photo->path)->assertExists($otherPhoto->path, 'other image');
});

test('returns 404 for a missing photo', function (string $method, string $uri): void {
    $response = $this->call($method, $uri);

    $response->assertNotFound();
})->with([
    'detail page' => ['GET', '/photos/999999'],
    'edit form' => ['GET', '/photos/999999/edit'],
    'update' => ['PUT', '/photos/999999'],
    'delete' => ['DELETE', '/photos/999999'],
]);
