<button class="chat-fab" id="chatFab" aria-label="Avaa chatti" aria-expanded="false" aria-controls="chatBox">&#128172;</button>
<section class="chat" id="chatBox" data-url="{{ route('chat.send') }}" aria-label="Chatti matkakodin avustajalle">
    <div class="chat-head">
        <div><strong>Kysy meiltä</strong><small>Avustaja vastaa heti</small></div>
        <button id="chatClose" aria-label="Sulje chatti">&times;</button>
    </div>
    <div class="chat-log" id="chatLog" aria-live="polite"></div>
    <div class="chat-suggest">
        <button type="button" data-suggest="Milloin aamupala tarjoillaan?">Aamupala</button>
        <button type="button" data-suggest="Mitä huoneet maksavat?">Hinnat</button>
        <button type="button" data-suggest="Mitkä huoneet ovat omalla vessalla?">Oma vessa</button>
    </div>
    <form class="chat-form" id="chatForm">
        <input type="text" id="chatInput" placeholder="Kirjoita kysymys…" maxlength="500" autocomplete="off" aria-label="Viesti">
        <button class="btn btn-sm" type="submit">Lähetä</button>
    </form>
</section>
