<?php

namespace App\Http\Controllers;

use App\Models\PriceItem;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'messages'           => ['required', 'array', 'min:1', 'max:30'],
            'messages.*.role'    => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:3000'],
        ]);

        $messages = array_map(
            fn ($m) => ['role' => $m['role'], 'content' => $m['content']],
            $data['messages']
        );

        while ($messages && $messages[0]['role'] !== 'user') {
            array_shift($messages);
        }

        $question = end($messages)['content'] ?? '';

        // Jos avain on asetettu, käytetään oikeaa tekoälyä. Muuten omaa moottoria.
        $key = config('matkakoti.anthropic_key');
        if ($key) {
            try {
                $res = Http::withHeaders([
                        'x-api-key'         => $key,
                        'anthropic-version' => '2023-06-01',
                    ])
                    ->timeout(60)
                    ->post('https://api.anthropic.com/v1/messages', [
                        'model'      => config('matkakoti.anthropic_model'),
                        'max_tokens' => 2000,
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
        }

        return response()->json(['reply' => $this->localAnswer($question)]);
    }

    // ------------------------------------------------------------------
    //  OMA VASTAUSMOOTTORI (ei tarvitse API-avainta)
    // ------------------------------------------------------------------

    private function localAnswer(string $original): string
    {
        $q = mb_strtolower(trim($original));

        // Apufunktio: löytyykö jokin sana (sanan alusta, jolloin taivutukset kelpaavat)
        $has = function (array $words) use ($q): bool {
            foreach ($words as $w) {
                if (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($w, '/') . '/u', $q)) {
                    return true;
                }
            }
            return false;
        };

        $phone   = config('matkakoti.phone');
        $email   = config('matkakoti.email');
        $address = config('matkakoti.address');

        // 1. Laskutoimitus, esim. "5 + 3" tai "12 * 4"
        if (preg_match('/(-?\d+(?:[.,]\d+)?)\s*([+\-*x×\/:])\s*(-?\d+(?:[.,]\d+)?)/u', $q, $m)) {
            $a = (float) str_replace(',', '.', $m[1]);
            $b = (float) str_replace(',', '.', $m[3]);
            $op = $m[2];
            if (in_array($op, ['/', ':'], true) && $b == 0.0) {
                return 'Nollalla ei voi jakaa. Matematiikka sanoo ei. 🙂';
            }
            $r = match ($op) {
                '+' => $a + $b,
                '-' => $a - $b,
                '/', ':' => $a / $b,
                default => $a * $b,
            };
            return "{$m[1]} {$op} {$m[3]} = " . rtrim(rtrim(number_format($r, 6, ',', ' '), '0'), ',');
        }

        // 2. Koodipyynnöt
        if ($has(['koodi', 'koodia', 'ohjelm', 'hello world', 'helloworld', 'skripti', 'tee mulle'])
            || $has(['c++', 'cpp', 'python', 'php', 'java', 'javascript', 'c#', 'html'])) {
            $code = $this->codeAnswer($q);
            if ($code !== null) {
                return $code;
            }
        }

        // 3. Hyvästit (ennen tervehdystä, koska "heippa" alkaa kuin "hei")
        if ($has(['heippa', 'moikka moi', 'moi moi', 'nähdään', 'hei hei', 'hyvästi', 'hyvää yötä', 'bye'])) {
            return 'Heippa ja kiitos juttutuokiosta! Tervetuloa käymään Lepää rauhassa -matkakotiin. 🌲';
        }

        // 4. Kuulumiset
        if ($has(['miten menee', 'mitä kuuluu', 'mitä sulle kuuluu', 'mitä teille kuuluu', 'how are you', 'kuis menee', 'miten sujuu', 'onko hyvä päivä'])) {
            return $this->pick([
                'Kiitos kysymästä, mulla menee tosi hyvin! Täällä on rauhallista kuin järven rannalla. Entä sinulla?',
                'Hyvin menee, kiitos! Tykkään kun täällä on juttuseuraa. Mitä sinulle kuuluu?',
                'Rauhallisesti ja hyvässä hengessä, kuten matkakodissa kuuluukin. Miten sinun päiväsi on sujunut?',
            ]);
        }
        if ($has(['hyvin', 'hyvää', 'ihan ok', 'tosi hyvin', 'mukavaa']) && mb_strlen($q) < 40) {
            return 'Kiva kuulla! Voin auttaa huoneissa, hinnoissa, aamupalassa ja varaamisessa, tai vaikka koodissa ja laskuissa. Mitä haluaisit tietää?';
        }

        // 5. Tervehdys
        if ($has(['moi', 'hei', 'terve', 'moikka', 'huomenta', 'hyvää päivää', 'hyvää iltaa', 'hello', 'hi', 'morjens', 'moro', 'hej'])) {
            return $this->pick([
                'Moi! Kiva kun tulit. Miten voin auttaa?',
                'Hei hei! Kysy vaikka huoneista, hinnoista tai aamupalasta. Voit myös jutella tai pyytää koodia.',
                'Terve! Olen matkakodin avustaja. Mitä haluaisit tietää?',
            ]);
        }

        // 6. Kiitos
        if ($has(['kiitos', 'kiitti', 'kiitoksia', 'thanks', 'thank you', 'thx'])) {
            return $this->pick(['Ole hyvä! Kysy ihmeessä lisää.', 'Eipä kestä! Muuta, missä voin auttaa?', 'Ilo on minun puolellani!']);
        }

        // 7. Kuka olet / mitä osaat
        if ($has(['kuka olet', 'kuka sä oot', 'kuka sä olet', 'mikä sä oot', 'mikä nimesi', 'mikä sun nimi', 'who are you', 'oletko botti', 'oletko robotti', 'oletko ihminen'])) {
            return 'Olen Lepää rauhassa -matkakodin verkkoavustaja, eli ohjelma enkä ihminen. En ole oikea tekoäly, vaan tunnistan kysymyksistä avainsanoja ja vastaan niihin. Siksi en aina ymmärrä kaikkea, mutta yritän parhaani.';
        }
        if ($has(['mitä osaat', 'mitä sä osaat', 'mitä voit', 'apua', 'auta', 'ohje', 'help', 'mitä tämä tekee', 'mitä voin kysyä'])) {
            return "Voin auttaa näissä:\n- huoneet ja niiden hinnat\n- aamupala ja yleiset tilat\n- varaaminen ja yhteystiedot\n- laskutoimitukset (esim. \"12 * 7\")\n- kellonaika ja päivämäärä\n- koodiesimerkit (C++, Python, PHP, Java, JavaScript, C#, HTML)\n- vitsit ja jutustelu\n\nKokeile vaikka \"tee c++ hello world\".";
        }

        // 8. Vitsit
        if ($has(['vitsi', 'vitsaa', 'kerro jotain hauskaa', 'hauska', 'joke', 'naurata'])) {
            return $this->pick([
                'Miksi ohjelmoija jäi pimeään? Koska hän ei löytänyt valoa (light) mistään, vaan pelkkiä bugeja.',
                'Mikä on kalan lempiruoka? Ruoto-pizza... ei, vaan "kalaa"! No joo, myönnän, tämä oli heikko.',
                'Kysyin majatalon pitäjältä, onko huoneessa hyvä näköala. Hän sanoi: "On, jos aukaiset verhot."',
                'Mitä sanoi nolla kasille? "Kiva vyö!"',
            ]);
        }

        // 9. Kello ja päivä
        if ($has(['kello', 'paljonko kello', 'mitä kello', 'what time'])) {
            return 'Kello on nyt ' . now('Europe/Helsinki')->format('H:i') . '.';
        }
        if ($has(['päivämäärä', 'mikä päivä', 'monesko päivä', 'mikä viikonpäivä', 'what date', 'tänään'])) {
            return 'Tänään on ' . now('Europe/Helsinki')->locale('fi')->isoFormat('dddd D.M.YYYY') . '.';
        }

        // 10. Sää
        if ($has(['sää', 'ilma', 'sataa', 'lämpötila', 'weather'])) {
            return 'En näe ajantasaista säätä, mutta ilmatieteenlaitos.fi kertoo sen tarkasti. Pakkaa varmuuden vuoksi sekä sadetakki että villapaita. 🙂';
        }

        // ----- MATKAKOTI -----

        if ($has(['aamupala', 'aamiai', 'breakfast', 'aamu'])) {
            return 'Aamupala tarjoillaan joka päivä klo 7:00–11:00 isossa huoneessa keittiön vieressä.';
        }
        if ($has(['ilta', 'ruoka', 'illallis', 'lounas', 'syödä', 'nälkä', 'mikro', 'jääkaap', 'kahvi', 'keitin', 'vedenkeitin', 'ravintola'])) {
            return 'Iltaisin ei ole ruokatarjontaa, mutta aamupalatilassa on vieraiden käytössä jääkaappi, mikroaaltouuni, kahvinkeitin ja vedenkeitin.';
        }
        if ($has(['vessa', 'wc', 'kylpy', 'suihku', 'sauna', 'pesu'])) {
            if ($has(['sauna'])) {
                return "Saunasta minulla ei ole varmaa tietoa. Kysy sitä suoraan: {$phone} tai {$email}.";
            }
            return 'Viidessä huoneessa on oma vessa, muissa on yhteiskäyttöinen vessa. Voit suodattaa huoneet omalla vessalla Huoneet-sivulla.';
        }
        if ($has(['hinta', 'hinn', 'maksa', 'kuinka paljon', 'paljonko', 'euro', '€', 'halpa', 'kallis', 'edullinen'])) {
            $min = Room::min('price_per_night');
            $max = Room::max('price_per_night');
            $cheapest = Room::orderBy('price_per_night')->first();
            $reply = 'Huoneet maksavat ' . number_format($min, 0, ',', ' ') . '–' . number_format($max, 0, ',', ' ') . ' € yöltä.';
            if ($cheapest) {
                $reply .= ' Edullisin on huone ' . $cheapest->number . ' "' . $cheapest->name . '".';
            }
            return $reply . ' Tarkemmat hinnat löydät Hinnasto-sivulta.';
        }
        if ($has(['huone', 'majoitus', 'yöpyä', 'nukkua', 'sänky', 'henkilö'])) {
            $count = Room::count();
            $own = Room::where('private_bathroom', true)->count();
            return "Meillä on {$count} huonetta, joista {$own}:ssa on oma vessa. Kuvat ja tiedot löydät Huoneet-sivulta, ja varaus onnistuu Varaa-sivulla.";
        }
        if ($has(['varaa', 'varaus', 'vapaa', 'saatavuus', 'book', 'reservation'])) {
            return 'Varaus onnistuu Varaa-sivulla: valitse saapumis- ja lähtöpäivä, niin näet vapaat huoneet. En itse voi tehdä varausta, mutta sivu opastaa askel kerrallaan.';
        }
        if ($has(['yhteys', 'puhelin', 'soita', 'numero', 'sähköposti', 'meili', 'email', 'osoite', 'missä', 'sijaitse', 'löydä', 'ajo-ohje'])) {
            return "Tavoitat meidät numerosta {$phone} tai sähköpostilla {$email}. Osoite: {$address}.";
        }
        if ($has(['palaute', 'arvostelu', 'arvio', 'kommentti', 'valitus'])) {
            return 'Palautetta voit antaa Palaute-sivulla. Kiitos että kerrot mielipiteesi!';
        }
        if ($has(['lemmik', 'koira', 'kissa', 'wifi', 'wlan', 'internet', 'parkki', 'pysäköi', 'auto', 'check', 'sisäänkirj', 'uloskirj', 'peruu', 'lapsi', 'lapset'])) {
            return "Tästä en ole varma, enkä halua arvailla väärin. Kysy suoraan: {$phone} tai {$email}.";
        }

        // Vastaus, jos mikään ei täsmännyt
        return $this->pick([
            'Tuota en valitettavasti ymmärtänyt. Voin kertoa huoneista, hinnoista, aamupalasta ja varaamisesta, tai tehdä koodiesimerkin ja laskea. Kokeile esim. "tee python hello world".',
            'Hmm, se meni minulta ohi. Kysy vaikka "mitä huoneet maksavat?", "milloin aamupala?" tai "kerro vitsi".',
        ]);
    }

    /** Palauttaa Hello World -esimerkin tunnistetulla kielellä, tai null. */
    private function codeAnswer(string $q): ?string
    {
        $is = fn (array $keys) => collect($keys)->contains(fn ($k) => str_contains($q, $k));

        if ($is(['c++', 'cpp', 'c plus'])) {
            return $this->codeBlock('cpp', <<<'CODE'
#include <iostream>

int main() {
    std::cout << "Hello, World!" << std::endl;
    return 0;
}
CODE, 'Käännä ja aja: g++ main.cpp -o hello && ./hello');
        }
        if ($is(['c#', 'csharp', 'c sharp'])) {
            return $this->codeBlock('csharp', <<<'CODE'
using System;

class Program
{
    static void Main()
    {
        Console.WriteLine("Hello, World!");
    }
}
CODE, 'Aja: dotnet run');
        }
        if ($is(['javascript', ' js', 'node'])) {
            return $this->codeBlock('javascript', <<<'CODE'
console.log("Hello, World!");
CODE, 'Aja: node hello.js (tai selaimen konsolissa).');
        }
        if ($is(['python', 'pyton'])) {
            return $this->codeBlock('python', <<<'CODE'
print("Hello, World!")
CODE, 'Aja: python3 hello.py');
        }
        if ($is(['php'])) {
            return $this->codeBlock('php', <<<'CODE'
<?php

echo "Hello, World!";
CODE, 'Aja: php hello.php');
        }
        if ($is(['java'])) {
            return $this->codeBlock('java', <<<'CODE'
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, World!");
    }
}
CODE, 'Käännä ja aja: javac Main.java && java Main');
        }
        if ($is(['html'])) {
            return $this->codeBlock('html', <<<'CODE'
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <title>Hello</title>
</head>
<body>
    <h1>Hello, World!</h1>
</body>
</html>
CODE, 'Tallenna tiedostoksi index.html ja avaa selaimessa.');
        }
        if (preg_match('/(?<![\p{L}\p{N}])c(?![\p{L}\p{N}+#])/u', $q)) {
            return $this->codeBlock('c', <<<'CODE'
#include <stdio.h>

int main(void) {
    printf("Hello, World!\n");
    return 0;
}
CODE, 'Käännä ja aja: gcc main.c -o hello && ./hello');
        }

        // Koodipyyntö ilman kieltä
        return 'Millä kielellä haluat koodin? Osaan tehdä esimerkin kielillä C++, C, C#, Python, PHP, Java, JavaScript ja HTML. Kirjoita vaikka "tee c++ hello world".';
    }

    private function codeBlock(string $lang, string $code, string $note): string
    {
        return "Tässä Hello World:\n\n```{$lang}\n{$code}\n```\n\n{$note}";
    }

    private function pick(array $options): string
    {
        return $options[array_rand($options)];
    }

    // ------------------------------------------------------------------
    //  Tekoälyn ohjeistus (käytössä vain, jos API-avain on asetettu)
    // ------------------------------------------------------------------

    private function systemPrompt(): string
    {
        $rooms = Room::orderBy('number')->get()->map(fn ($r) => sprintf(
            '- Huone %s "%s": %s, %d hlö, %s €/yö, %s',
            $r->number, $r->name, $r->private_bathroom ? 'oma vessa' : 'yhteiskäyttöinen vessa',
            $r->capacity, number_format($r->price_per_night, 0, ',', ' '),
            Str::limit($r->description, 200)
        ))->implode("\n");

        $prices = PriceItem::orderBy('sort')->get()->map(fn ($p) => sprintf(
            '- %s: %s € %s', $p->title, number_format($p->price, 0, ',', ' '), $p->unit
        ))->implode("\n");

        $name = config('matkakoti.name');
        $phone = config('matkakoti.phone');
        $email = config('matkakoti.email');
        $address = config('matkakoti.address');

        return <<<PROMPT
Olet {$name} -matkakodin ystävällinen avustaja ja samalla taitava yleisavustaja. Vastaat suomeksi (tai käyttäjän kielellä), lämpimästi ja selkeästi. Voit jutella, selittää asioita ja kirjoittaa koodia millä kielellä tahansa. Laita koodi aina kolmen backtickin lohkoon.

MATKAKOTI: vanha maatila, 20 huonetta (5:ssä oma vessa). Aamupala joka päivä klo 7:00–11:00. Iltaisin ei ruokaa. Yhteystiedot: {$phone}, {$email}, {$address}. Varaus Varaa-sivulla.

HUONEET:
{$rooms}

HINNASTO:
{$prices}

Matkakodin omissa asioissa käytä vain yllä olevia tietoja äläkä keksi mitään. Jos et tiedä, ohjaa soittamaan tai laittamaan sähköpostia.
PROMPT;
    }
}