<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use App\Models\Room;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPhotosTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_photos_are_shown_in_the_public_gallery(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertSee('Kesäjärvi')
            ->assertSee('Revontulet Lapissa');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/gallery/breakfast-pixabay.jpg');

        $images = GalleryImage::query()->get();

        $this->assertCount(6, $images);

        foreach ($images as $image) {
            $this->assertStringContainsString('Pixabaysta', $image->caption);
            $this->assertFileExists(public_path($image->path));
        }
    }

    public function test_homepage_and_booking_results_show_pixabay_photos(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['spring', 'summer', 'autumn', 'winter'] as $season) {
            $this->get(route('home', ['season' => $season]))
                ->assertOk()
                ->assertSee("images/seasons/{$season}.jpg");
        }

        $rooms = Room::query()->get();
        $response = $this->get(route('booking.search', [
            'check_in' => today()->addDay()->toDateString(),
            'check_out' => today()->addDays(2)->toDateString(),
            'guests' => 1,
        ]));

        $response->assertOk();
        $this->assertCount(20, $rooms);

        foreach ($rooms as $room) {
            $this->assertFileExists(public_path($room->image));
            $response->assertSee($room->image_url);
        }
    }
}
