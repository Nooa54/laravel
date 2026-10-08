<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\GalleryImage;
use App\Models\PriceItem;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Testikäyttäjä (Jetstream-kirjautuminen): vieras@example.com / salasana123
        User::firstOrCreate(
            ['email' => 'vieras@example.com'],
            ['name' => 'Testi Vieras', 'password' => Hash::make('salasana123')]
        );

        // ----- 20 huonetta, joista 5:ssä oma vessa (huoneet 6–10) -----
        $names = [
            1 => 'Aitta', 2 => 'Navetan kamari', 3 => 'Pirtti', 4 => 'Tupa', 5 => 'Vilja',
            6 => 'Ruusukammari', 7 => 'Järvinäkymä', 8 => 'Pihlaja', 9 => 'Koivu', 10 => 'Isännän huone',
            11 => 'Vintti', 12 => 'Heinäsuova', 13 => 'Kellari', 14 => 'Kuisti', 15 => 'Mansardi',
            16 => 'Laidun', 17 => 'Peltotie', 18 => 'Metsänreuna', 19 => 'Sauna-aitta', 20 => 'Perhehuone',
        ];
        $lorem = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Rauhallinen huone pihan puolella, jossa on pehmeät sängyt ja iso ikkuna.',
            'Sed ut perspiciatis unde omnis iste natus. Valoisa huone, jossa vanhan talon tunnelma säilyy mutta sängyt ovat uudet.',
            'Nemo enim ipsam voluptatem quia voluptas sit. Hiljainen huone peltojen puolella, aamuaurinko paistaa suoraan sisään.',
            'Ut enim ad minima veniam, quis nostrum. Tilava huone, jossa on pieni työpöytä ja riittävästi tilaa matkalaukuille.',
        ];

        foreach ($names as $n => $name) {
            $bath = in_array($n, [6, 7, 8, 9, 10]);
            $family = $n === 20;
            Room::updateOrCreate(['number' => (string) $n], [
                'name'             => $name,
                'description'      => $lorem[$n % 4] . ($bath ? ' Huoneessa on oma vessa.' : ' Vessa on käytävällä, yhteiskäytössä.'),
                'price_per_night'  => $family ? 119 : ($bath ? 89 + ($n - 6) * 4 : 59 + ($n % 5) * 3),
                'capacity'         => $family ? 4 : ($n % 3 === 0 ? 3 : 2),
                'private_bathroom' => $bath,
                'image'            => $family ? 'images/rooms/room-family.svg'
                                    : ($bath ? 'images/rooms/room-bath.svg'
                                    : ($n >= 11 && $n % 2 ? 'images/rooms/room-attic.svg' : 'images/rooms/room-default.svg')),
            ]);
        }

        // ----- Hinnasto -----
        if (PriceItem::count() === 0) {
            foreach ([
                ['Kahden hengen huone, yhteinen vessa', 'Aamupala sisältyy', 59, '/ yö', 1],
                ['Kahden hengen huone, oma vessa', 'Aamupala sisältyy', 89, '/ yö', 2],
                ['Perhehuone (4 hlö)', 'Oma vessa, aamupala sisältyy', 119, '/ yö', 3],
                ['Lisävuode', 'Pyydä varauksen yhteydessä', 15, '/ yö', 4],
                ['Aamupala ulkopuoliselle', 'Klo 7:00–11:00', 12, '/ hlö', 5],
                ['Loppusiivous', 'Ei veloiteta normaalissa majoituksessa', 0, '/ vierailu', 6],
            ] as [$title, $desc, $price, $unit, $sort]) {
                PriceItem::create(compact('title', 'price', 'unit', 'sort') + ['description' => $desc]);
            }
        }

        // ----- Galleria -----
        if (GalleryImage::count() === 0) {
            foreach ([
                ['Kevät pihalla', 'Omenapuut kukkivat toukokuussa', 'images/seasons/spring.svg', 'spring'],
                ['Kesäilta', 'Peltojen yllä pitkät valoisat illat', 'images/seasons/summer.svg', 'summer'],
                ['Ruska', 'Syksyn värit tilan ympärillä', 'images/seasons/autumn.svg', 'autumn'],
                ['Talviyö', 'Lunta ja revontulia', 'images/seasons/winter.svg', 'winter'],
                ['Aamupalasali', 'Aamupala klo 7–11 keittiön vieressä', 'images/gallery/breakfast.svg', null],
                ['Huone 6', 'Oma vessa ja ikkuna peltoon', 'images/rooms/room-bath.svg', null],
                ['Vintin huone', 'Kattoparrut ja lämmin valo', 'images/rooms/room-attic.svg', null],
                ['Perhehuone', 'Tilaa neljälle', 'images/rooms/room-family.svg', null],
            ] as [$title, $caption, $path, $season]) {
                GalleryImage::create(compact('title', 'caption', 'path', 'season'));
            }
        }

        // ----- Arvosteluja -----
        if (Review::count() === 0) {
            $r6 = Room::where('number', '6')->first();
            $r2 = Room::where('number', '2')->first();
            $r20 = Room::where('number', '20')->first();
            Review::insert([
                ['room_id' => $r6->id, 'author_name' => 'Maija K.', 'rating' => 5, 'comment' => 'Todella rauhallista. Aamupala oli runsas ja huone siisti.', 'created_at' => now()->subDays(12), 'updated_at' => now()],
                ['room_id' => $r2->id, 'author_name' => 'Jussi L.', 'rating' => 4, 'comment' => 'Hyvä hinta-laatu. Yhteinen vessa oli puhdas ja keittiötila kätevä iltaisin.', 'created_at' => now()->subDays(30), 'updated_at' => now()],
                ['room_id' => $r20->id, 'author_name' => 'Perhe Virtanen', 'rating' => 5, 'comment' => 'Lapset rakastivat maatilaa. Huoneessa oli tilaa kaikille.', 'created_at' => now()->subDays(45), 'updated_at' => now()],
            ]);
        }

        // ----- Yksi esimerkkivaraus -----
        if (Booking::count() === 0) {
            $demoRoom = Room::where('number', '2')->first();
            Booking::create([
                'reference' => 'DEMO2026', 'room_id' => $demoRoom->id, 'guest_name' => 'Demo Vieras', 'guest_email' => 'demo@example.com',
                'check_in' => today()->addDays(10), 'check_out' => today()->addDays(12), 'guests' => 2,
                'total_price' => 2 * 62, 'status' => 'confirmed',
            ]);
        }
    }
}
