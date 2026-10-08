(function () {
  // Mobiilivalikko
  var toggle = document.getElementById('navToggle');
  var menu = document.getElementById('navMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Gallerian lightbox
  var lb = document.getElementById('lightbox');
  if (lb) {
    var lbImg = lb.querySelector('img');
    var lbCap = lb.querySelector('p');
    document.querySelectorAll('[data-lightbox]').forEach(function (el) {
      el.addEventListener('click', function () {
        lbImg.src = el.dataset.src;
        lbImg.alt = el.dataset.title || '';
        lbCap.textContent = el.dataset.title + (el.dataset.caption ? ' – ' + el.dataset.caption : '');
        lb.classList.add('open');
      });
    });
    lb.addEventListener('click', function () { lb.classList.remove('open'); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') lb.classList.remove('open'); });
  }

  // Varauslomakkeen päivämäärät: lähtö aina saapumisen jälkeen
  var ci = document.querySelector('input[name=check_in][data-dates]');
  var co = document.querySelector('input[name=check_out][data-dates]');
  if (ci && co) {
    ci.addEventListener('change', function () {
      var d = new Date(ci.value);
      if (isNaN(d)) return;
      d.setDate(d.getDate() + 1);
      var min = d.toISOString().slice(0, 10);
      co.min = min;
      if (!co.value || co.value < min) co.value = min;
    });
  }

  // Chatti
  var fab = document.getElementById('chatFab');
  var box = document.getElementById('chatBox');
  if (!fab || !box) return;
  var log = document.getElementById('chatLog');
  var form = document.getElementById('chatForm');
  var input = document.getElementById('chatInput');
  var history = [];
  var greeted = false;

  function add(role, text, cls) {
    var d = document.createElement('div');
    d.className = 'msg ' + (role === 'user' ? 'me' : 'bot') + (cls ? ' ' + cls : '');
    d.textContent = text;
    log.appendChild(d);
    log.scrollTop = log.scrollHeight;
    return d;
  }

  function open() {
    box.classList.add('open');
    fab.setAttribute('aria-expanded', 'true');
    if (!greeted) {
      greeted = true;
      add('assistant', 'Hei! Olen matkakodin avustaja. Voin kertoa huoneista, hinnoista, aamupalasta ja varaamisesta. Mitä haluaisit tietää?');
    }
    input.focus();
  }
  function close() { box.classList.remove('open'); fab.setAttribute('aria-expanded', 'false'); }

  fab.addEventListener('click', function () { box.classList.contains('open') ? close() : open(); });
  document.getElementById('chatClose').addEventListener('click', close);

  async function send(text) {
    if (!text.trim()) return;
    add('user', text);
    history.push({ role: 'user', content: text });
    var typing = add('assistant', 'Kirjoittaa…', 'typing');
    try {
      var res = await fetch(box.dataset.url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ messages: history.slice(-12) })
      });
      var data = await res.json();
      typing.remove();
      var reply = data.reply || 'Hups, en saanut vastausta. Kokeile hetken päästä uudelleen.';
      add('assistant', reply);
      history.push({ role: 'assistant', content: reply });
    } catch (e) {
      typing.remove();
      add('assistant', 'Yhteys katkesi. Soita meille tai kokeile hetken päästä uudelleen.');
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var t = input.value;
    input.value = '';
    send(t);
  });
  document.querySelectorAll('[data-suggest]').forEach(function (b) {
    b.addEventListener('click', function () { open(); send(b.dataset.suggest); });
  });
})();
