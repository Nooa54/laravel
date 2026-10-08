<?php

namespace App\Http\Controllers;

use App\Models\PriceItem;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'messages'           => ['required', 'array', 'min:1', 'max:20'],
            'messages.*.role'    => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:1500'],
        ]);

        $messages = array_map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']], $data['messages']);

        // Anthropic-rajapinta vaatii, että ensimmäinen viesti on käyttäjältä
        while ($messages && $messages[0]['role'] !== 'user') {
            array_shift($messages);
        }

        $key = config('matkakoti.anthropic_key');
        if (! $key) {
            return response()->json(['reply' => $this->fallback(end($messages)['content'] ?? '')]);
        }

        try {
            $res = Http::withHeaders([
                    'x-api-key'         => $key,
                    'anthropic-version' => '2023-06-01',
                ])
                ->timeout(30)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model'      => config('matkakoti.anthropic_model'),
                    'max_tokens' => 600,
                    'system'     => $this->systemPrompt(),
                    'messages'   => $messages,
                ]);

            if ($res->successful()) {
                $text = collect($res->json('content', []))->where('type', 'text')->pluck('text')->implode("\n");
                if ($text !== '') {
                    return response()->json(['reply' => $text]);
                }
            }
            Log::warning('Chatbot API error', ['status' => $res->status(), 'body' => $res->body()]);
        } catch (\Throwable $e) {
            Log::warning('Chatbot exception: ' . $e->getMessage());
        }

        return response()->json(['reply' => $this->fallback(end($messages)['content'] ?? '')]);
    }

    private function systemPrompt(): string
    {
        $rooms = Room::orderBy('number')->get()->map(fn ($r) => sprintf(
            '- Huone %s "%s": %s, %d hlö, %s €/yö, %s',
            $r->number, $r->name, $r->private_bathroom ? 'oma vessa' : 'yhteiskäyttöinen vessa',
            $r->capacity, number_format($r->price_per_night, 0, ',', ' '),
            \Illuminate\Support\Str::limit($r->description, 90)
        ))->implode("\n");

        $prices = PriceItem::orderBy('sort')->get()->map(fn ($p) => sprintf(
            '- %s: %s € %s', $p->title, number_format($p->price, 0, ',', ' '), $p->unit
        ))->implode("\n");

        $name = config('matkakoti.name');
        $phone = config('matkakoti.phone');
        $email = config('matkakoti.email');
        $address = config('matkakoti.address');

        return <<<PROMPT
Olet {$name} -matkakodin ystävällinen verkkosivuston avustaja. Vastaat suomeksi (tai asiakkaan kielellä), lyhyesti ja lämpimästi, enintään noin 4 lausetta kerrallaan.

TIEDOT MATKAKODISTA:
- Vanha maatila, jossa on matkakoti. 20 huonetta, joista 5:ssä on oma vessa. Muissa huoneissa on yhteiskäyttöinen vessa.
- Aamupala tarjoillaan joka päivä klo 7:00–11:00 isossa huoneessa keittiön vieressä.
- Iltaisin ruokaa ei ole tarjolla. Samassa tilassa aamupalan kanssa on asiakkaiden käytössä jääkaappi, mikroaaltouuni, kahvinkeitin ja vedenkeitin.
- Yhteystiedot: {$phone}, {$email}, {$address}.
- Varaus tehdään sivuston kohdasta "Varaa" (valitse päivät, sitten vapaa huone). Palautetta voi antaa kohdassa "Palaute".

HUONEET:
{$rooms}

HINNASTO:
{$prices}

SÄÄNNÖT:
- Käytä vain yllä olevia tietoja. Jos et tiedä (esim. lemmikit, sauna, check-in-aika), sano ettet ole varma ja ohjaa soittamaan tai laittamaan sähköpostia.
- Et voi tehdä varauksia itse tai tarkistaa saatavuutta. Ohjaa asiakas Varaa-sivulle.
- Älä keksi hintoja tai palveluita.
PROMPT;
    }

    /** Yksinkertainen varavastaus, jos ANTHROPIC_API_KEY puuttuu tai rajapinta ei vastaa. */
    private function fallback(string $q): string
    {
        $q = mb_strtolower($q);
        $has = fn (array $words) => collect($words)->contains(fn ($w) => str_contains($q, $w));

        if ($has(['aamupala', 'aamiai', 'breakfast'])) {
            return 'Aamupala tarjoillaan joka päivä klo 7:00–11:00 isossa huoneessa keittiön vieressä.';
        }
        if ($has(['ilta', 'ruoka', 'illallis', 'mikro', 'jääkaap', 'kahvi', 'keitin'])) {
            return 'Iltaisin ei ole ruokatarjontaa, mutta aamupalatilassa on vieraiden käytössä jääkaappi, mikroaaltouuni, kahvinkeitin ja vedenkeitin.';
        }
        if ($has(['vessa', 'wc', 'kylpy', 'suihku'])) {
            return 'Viidessä huoneessa on oma vessa, muissa on yhteiskäyttöinen vessa. Voit suodattaa huoneet omalla vessalla Huoneet-sivulla.';
        }
        if ($has(['hinta', 'maksa', 'kuinka paljon', '€', 'euro'])) {
            $min = Room::min('price_per_night'); $max = Room::max('price_per_night');
            return 'Huoneet maksavat ' . number_format($min, 0, ',', ' ') . '–' . number_format($max, 0, ',', ' ') . ' € yöltä. Tarkemmat hinnat löydät Hinnasto-sivulta.';
        }
        if ($has(['varaa', 'varaus', 'vapaa', 'book'])) {
            return 'Varaus onnistuu Varaa-sivulla: valitse saapumis- ja lähtöpäivä, niin näet vapaat huoneet.';
        }
        if ($has(['yhteys', 'puhelin', 'sähköposti', 'osoite', 'missä'])) {
            return 'Tavoitat meidät numerosta ' . config('matkakoti.phone') . ' tai sähköpostilla ' . config('matkakoti.email') . '. Osoite: ' . config('matkakoti.address') . '.';
        }
        return 'Voin kertoa aamupalasta, huoneista, hinnoista ja varaamisesta. Mitä haluaisit tietää?';
    }
}
