# Todo-toiminnon muutosohje

Todo-toiminto löytyy kirjautuneen käyttäjän valikosta kohdasta **Tehtävälista**. Sovelluksen osoitteessa `/todo` voi lisätä tehtävän, avata sen, muokata tietoja ja poistaa sen.

## Mitä kukin tiedosto tekee

- `app/Models/Todo.php`: Todo-tietueen sallitut kentät, päivämäärän tyyppi ja tila-/kiireellisyysvaihtoehdot.
- `app/Models/User.php`: käyttäjän Todo-suhde. Kaikki toiminnot rajaavat tietueet tähän suhteeseen.
- `app/Http/Controllers/TodoController.php`: CRUD-toiminnot sekä lomakearvojen palvelinpuolen tarkistus.
- `database/migrations/2026_10_01_093221_create_todos_table.php`: Todo-taulun sarakkeet ja käyttäjän viiteavain.
- `resources/views/todo/index.blade.php`: oma tehtävälista.
- `resources/views/todo/create.blade.php`: uuden tehtävän sivu.
- `resources/views/todo/edit.blade.php`: tehtävän muokkaussivu.
- `resources/views/todo/show.blade.php`: yhden tehtävän tiedot.
- `resources/views/todo/_form.blade.php`: lisäys- ja muokkauslomakkeen yhteiset kentät.
- `routes/web.php`: `todo.*`-reitit. Reitit vaativat kirjautumisen.
- `resources/views/navigation-menu.blade.php`: tehtävälistan linkki tietokone- ja mobiilivalikossa.

## Kentät

Tehtävässä on nimi, vapaaehtoinen kuvaus, tila, vapaaehtoinen määräpäivä ja kiireellisyys. Tilan vaihtoehdot ovat `idea`, `toteutetaan`, `aloitettu`, `tehty 50%`, `tehty 70%`, `valmis`, `hylätään ehdotus` ja `tehdään seuraavaan versioon`. Kiireellisyysvaihtoehdot ovat `Ei kiirettä`, `heti` ja `eilen`.

## Muutosten järjestys

1. Muuta tietokantarakennetta uudella migraatiolla. Älä käytä `migrate:fresh`- tai rollback-komentoa tässä projektissa, koska se voi poistaa tietoja tai kumota muitakin migraatioita.
2. Päivitä `Todo`-mallin sallitut kentät ja vaihtoehdot.
3. Päivitä kontrollerin validointi ja tallennus käyttäjän oman `todos()`-suhteen kautta.
4. Päivitä yhteinen `_form` ja sen jälkeen lista-, lisäys-, näyttö- ja muokkaussivut.
5. Päivitä reitit sekä sekä leveä että mobiili navigaatio.
6. Aja `php artisan test --compact tests/Feature/TodoCrudTest.php`, sitten `vendor/bin/pint --dirty --format agent` ja tarkista sivut selaimessa.

Nykyinen sovellus käyttää paikallisesti MySQL-tietokantaa, ja yksi aiempi `palautes`-migraatio on ollut odottamassa. Todo-migraation voi ajaa yksin komennolla `php artisan migrate --path=database/migrations/2026_10_01_093221_create_todos_table.php`. Tämä jättää muut odottavat migraatiot rauhaan.

## Palautuskuvat

- [todo@index: tehtävälista](todo-kuvakaappaukset/todo-index.png)
- [todo@create: lisää tehtävä](todo-kuvakaappaukset/todo-create.png)
- [todo@edit: muokkaa tehtävää](todo-kuvakaappaukset/todo-edit.png)
- [todo@controller: TodoController-koodi](todo-kuvakaappaukset/todo-controller.png)
