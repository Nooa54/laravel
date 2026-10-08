# Blade-tiedostojen muokkausmuistilista

Tämä on projektikohtainen muistilista Laravelin Blade-näkymistä. Avaa tämä tiedosto uudelleen myöhemmin ja muokkaa listaa sitä mukaa, kun opit projektista lisää.

## Ennen muokkaamista

- Käynnistä projektin juuressa kaksi terminaalia: toisessa `php artisan serve` ja toisessa `npm run dev`. Jätä molemmat käyntiin, kun testaat sivua.
- Avaa selaimessa Laravelin osoite, tavallisesti `http://127.0.0.1:8000`.
- Muokkaa yhtä näkymää kerrallaan. Tallenna tiedosto ja päivitä selain. Blade- ja CSS-muutokset eivät tavallisesti vaadi palvelimien uudelleenkäynnistystä.
- Tee pieni muutos yhteen näkyvään tekstiin, jotta löydät sivulta muokatun kohdan. Tarkista sama kohta myös kapealla ja leveällä näytöllä.
- Pidä kirjautuneen käyttäjän ja vierailijan näkymät erillään: näkymissä käytetään esimerkiksi `@auth`, `@else` ja `@if`-ehtoja.

## Sivut ja niiden tarkoitus

| Tiedosto | Mitä tiedosto näyttää tai ohjaa | Huomio muokkaamiseen |
| --- | --- | --- |
| [welcome.blade.php](../views/welcome.blade.php) | Laravelin aloituspohja. Yläreunassa on kirjautumislinkit, ja sisältöalueella Laravelin tervetuloteksti ja linkit. | Tällä hetkellä `routes/web.php`-tiedoston `/`-reitti ohjaa dashboardiin; se ei palauta tätä näkymää. Siksi `welcome.blade.php`-muutos ei näy etusivun osoitteessa nykyisillä reiteillä. Tiedostossa on myös pitkä sisäänrakennettu CSS-varasisältö, joten etsi muokattava teksti HTML-osasta äläkä muuta varasisältöä vahingossa. |
| [dashboard.blade.php](../views/dashboard.blade.php) | Kirjautuneen käyttäjän dashboard-sivu. Käyttää `<x-app-layout>`-pohjaa ja näyttää otsikon sekä `<x-welcome />`-komponentin. | Reitti `/dashboard` vaatii kirjautumisen. Dashboardin sisältöä voi tulla myös `components/welcome.blade.php`-tiedostosta. |
| [auth/login.blade.php](../views/auth/login.blade.php) | Kirjautumislomake: sähköposti, salasana, muista minut, salasanan palautuslinkki, kirjautumispainike ja virhe-/tilaviestit. | Käyttää vierasnäkymän layoutia sekä Jetstreamin valmiita lomakekomponentteja. |
| [auth/register.blade.php](../views/auth/register.blade.php) | Rekisteröitymislomake: nimi, sähköposti, salasana ja salasanan vahvistus. | Käyttöehto- ja tietosuojavalinta näkyy, jos Jetstreamin vastaava ominaisuus on käytössä. |
| [layouts/app.blade.php](../views/layouts/app.blade.php) | Kirjautuneiden sivujen yhteinen runko: banneri, navigaatio, valinnainen otsake, sivun sisältöpaikka ja Livewire-skriptit. | Layoutin muutos voi näkyä kaikilla kirjautuneiden sivuilla. Responsiivinen navigaatio tulee `navigation-menu`-komponentista. |
| [layouts/guest.blade.php](../views/layouts/guest.blade.php) | Kirjautumattomien sivujen yhteinen runko; näyttää komponentin sisällön `$slot`-paikassa. | Muutos voi näkyä esimerkiksi kirjautumis- ja rekisteröitymissivuilla. |
| [components/welcome.blade.php](../views/components/welcome.blade.php) | Dashboardin tervetuloteksti, logo ja Laravelin dokumentaatio-, Laracasts-, Tailwind- ja tunnistautumisosuudet. | Tätä komponenttia kutsuu dashboard. Sisältö on omassa tiedostossaan, vaikka sitä ei näe dashboardin tiedoston tekstissä. |
| [navigation-menu.blade.php](../views/navigation-menu.blade.php) | Kirjautuneiden valikko: Dashboard, Yhteystiedot, Info ja Palaute sekä käyttäjätilin valikko ja uloskirjautuminen. | Tiedostossa on erilliset leveän näytön ja mobiilin valikot. Mobiilivalikko avataan hampurilaispainikkeesta. Muokkaus voi vaatia molempien osien päivittämistä. |

## Responsiivisuus ja ehdollinen sisältö

Useissa näkymissä käytetään Tailwind-luokkia kuten `sm:`, `md:` ja `lg:`. Ne muuttavat asettelua eri levyisillä näytöillä. Valikossa leveän näytön linkit näytetään `sm:flex`-luokalla, kun taas mobiilivalikko käyttää `sm:hidden`-luokkaa. Jos muutat valikkoa, tarkista sekä tietokone- että puhelinnäkymä.

Etusivun kirjautumislinkit on ehdollistettu reitin ja kirjautumistilan mukaan: kirjautuneelle näytetään dashboard-linkki ja vierailijalle kirjautuminen sekä mahdollisesti rekisteröityminen. `layouts/app.blade.php` ja navigaatio ovat kirjautuneiden näkymiä; `layouts/guest.blade.php` on vierasnäkymiä varten.

## Pieni testauskierros

1. Valitse yksi tiedosto ja kirjoita muistiin, mitä kohtaa aiot muuttaa.
2. Etsi tiedostosta näkyvä teksti tai luokka; älä aloita suuresta rakenteen muutoksesta.
3. Lisää tai muuta yhtä sanaa, tallenna tiedosto ja päivitä sopiva sivu selaimessa.
4. Tarkista, että näet muutoksen oikealla sivulla. Jos et näe sitä, tarkista ensin `routes/web.php`, kirjautumistila ja se, käyttääkö sivu komponenttia tai layoutia.
5. Tarkista muutos sekä kapeassa että leveässä näkymässä. Palauta kokeiluteksti tai jätä se vain, jos se kuuluu sivun lopulliseen sisältöön.
6. Siirry vasta tämän jälkeen seuraavaan tiedostoon.

## Reittimuistio

Nykyisessä `routes/web.php`-tiedostossa `/` ohjaa `/dashboard`-reitille. Dashboard, yhteystiedot, info ja palaute vaativat kirjautumisen. Tämä selittää, miksi satunnainen vierailija näkee kirjautumisen eikä kirjautuneen käyttäjän valikkoa.
