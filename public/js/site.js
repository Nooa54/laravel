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
  var convId = 0; // kasvaa aina kun aloitetaan uusi keskustelu

  // Näyttää viestin turvallisesti: koodilohkot, **lihavointi** ja `koodi`
  function renderMessage(el, text) {
    el.textContent = '';

    function addInline(parent, str) {
      str.split(/(\*\*[^*]+\*\*|`[^`]+`)/).forEach(function (seg) {
        if (!seg) return;
        var node;
        if (seg.startsWith('**') && seg.endsWith('**') && seg.length > 4) {
          node = document.createElement('strong');
          node.textContent = seg.slice(2, -2);
        } else if (seg.startsWith('`') && seg.endsWith('`') && seg.length > 2) {
          node = document.createElement('code');
          node.textContent = seg.slice(1, -1);
        } else {
          node = document.createElement('span');
          node.className = 'txt';
          node.textContent = seg;
        }
        parent.appendChild(node);
      });
    }

    text.split('```').forEach(function (part, i) {
      if (i % 2 === 0) {
        if (part.trim()) {
          var p = document.createElement('div');
          p.className = 'txt-block';
          addInline(p, part.replace(/^\n+|\n+$/g, ''));
          el.appendChild(p);
        }
        return;
      }
      var nl = part.indexOf('\n');
      var lang = nl > -1 ? part.slice(0, nl).trim() : '';
      var code = (nl > -1 ? part.slice(nl + 1) : part).replace(/\n$/, '');

      var wrap = document.createElement('div');
      wrap.className = 'code-wrap';
      var bar = document.createElement('div');
      bar.className = 'code-bar';
      var label = document.createElement('span');
      label.textContent = lang || 'koodi';
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = 'Kopioi';
      btn.addEventListener('click', function () {
        navigator.clipboard.writeText(code).then(function () {
          btn.textContent = 'Kopioitu!';
          setTimeout(function () { btn.textContent = 'Kopioi'; }, 1500);
        });
      });
      bar.appendChild(label);
      bar.appendChild(btn);
      var pre = document.createElement('pre');
      var c = document.createElement('code');
      c.textContent = code;
      pre.appendChild(c);
      wrap.appendChild(bar);
      wrap.appendChild(pre);
      el.appendChild(wrap);
    });
  }

  function add(role, text, cls) {
    var d = document.createElement('div');
    d.className = 'msg ' + (role === 'user' ? 'me' : 'bot') + (cls ? ' ' + cls : '');
    renderMessage(d, text);
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
  var closeBtn = document.getElementById('chatClose');
  closeBtn.addEventListener('click', close);

  // Uusi keskustelu -nappi (lisätään sulje-napin viereen)
  var newBtn = document.createElement('button');
  newBtn.type = 'button';
  newBtn.id = 'chatNew';
  newBtn.title = 'Aloita uusi keskustelu';
  newBtn.setAttribute('aria-label', 'Aloita uusi keskustelu');
  newBtn.textContent = '↻ Uusi';
  closeBtn.parentNode.insertBefore(newBtn, closeBtn);

  newBtn.addEventListener('click', function () {
    convId++;            // mitätöi mahdollisen kesken olevan vastauksen
    history.length = 0;  // tyhjentää keskusteluhistorian
    log.innerHTML = '';  // tyhjentää viestit näkyvistä
    greeted = false;
    open();              // näyttää tervehdyksen uudelleen ja asettaa kohdistimen kenttään
  });

  async function send(text) {
    if (!text.trim()) return;
    var myConv = convId;
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
      if (myConv !== convId) return; // keskustelu vaihtui kesken vastauksen
      typing.remove();
      var reply = data.reply || 'Hups, en saanut vastausta. Kokeile hetken päästä uudelleen.';
      add('assistant', reply);
      history.push({ role: 'assistant', content: reply });
    } catch (e) {
      if (myConv !== convId) return;
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