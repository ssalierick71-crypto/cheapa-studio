/* ============================================================
   CHEAPA STUDIO — main.js
   Nav drawer · Cart (localStorage + WhatsApp checkout) ·
   Chatbot · Shop filters · Pack stage selector
   ============================================================ */
(function () {
  'use strict';
  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const WA = (window.CHEAPA && window.CHEAPA.wa) || '256700000000';
  const CUR = (window.CHEAPA && window.CHEAPA.currency) || 'UGX';
  const money = n => CUR + ' ' + Number(n).toLocaleString('en-US');

  /* ---------- Mobile drawer ---------- */
  const menu = $('#mobileMenu'), burger = $('#hamburgerBtn');
  function toggleMenu(open) {
    if (!menu) return;
    menu.classList.toggle('open', open);
    burger && burger.classList.toggle('open', open);
    burger && burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
  }
  burger && burger.addEventListener('click', () => toggleMenu(!menu.classList.contains('open')));
  $$('[data-close-menu]').forEach(el => el.addEventListener('click', () => toggleMenu(false)));

  /* ============================================================
     CART
     ============================================================ */
  const CART_KEY = 'cheapa_cart_v2';
  const drawer = $('#cartDrawer');
  let cart = [];
  try { cart = JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch (e) { cart = []; }
  // migrate/clean any malformed entries
  cart = cart.filter(i => i && i.unitPrice != null && i.qty != null);
  const saveCart = () => localStorage.setItem(CART_KEY, JSON.stringify(cart));

  const lineTotal = it => it.unitPrice * it.qty + (it.design ? it.designFee : 0);
  function cartTotal() { return cart.reduce((s, i) => s + lineTotal(i), 0); }
  // expose for the checkout page
  window.CHEAPA_CART = { read: () => cart, total: cartTotal, clear: () => { cart = []; saveCart(); } };

  function renderCart() {
    const badge = $('#cartCount');
    if (badge) { badge.textContent = cart.length; badge.hidden = cart.length === 0; }

    const wrap = $('#cartItems'), empty = $('#cartEmpty'), foot = $('#cartFoot');
    if (!wrap) return;
    if (!cart.length) {
      wrap.innerHTML = '';
      empty && (empty.style.display = 'flex');
      foot && (foot.hidden = true);
      return;
    }
    empty && (empty.style.display = 'none');
    foot && (foot.hidden = false);
    wrap.innerHTML = cart.map((it, i) => `
      <div class="cart-item">
        <div class="cart-item-ico"><i class="bi ${it.icon || 'bi-bag'}"></i></div>
        <div class="cart-item-main">
          <div class="cart-item-name">${esc(it.name)}${it.variant ? ` <span class="cart-variant">${esc(it.variant)}</span>` : ''}</div>
          <div class="cart-item-price">${money(it.unitPrice)} / ${esc(unitOne(it.unitLabel))}${it.design ? ` · +design ${money(it.designFee)}` : ''}</div>
          <div class="cart-qty">
            <button data-dec="${i}" aria-label="Decrease">−</button>
            <span>${it.qty}${it.unitLabel ? ' ' + esc(it.unitLabel) : ''}</span>
            <button data-inc="${i}" aria-label="Increase">+</button>
          </div>
        </div>
        <div style="text-align:right">
          <div class="cart-line-total">${money(lineTotal(it))}</div>
          <button class="cart-item-remove" data-rm="${i}" aria-label="Remove"><i class="bi bi-trash"></i></button>
        </div>
      </div>`).join('');
    const total = $('#cartTotal'); total && (total.textContent = money(cartTotal()));
  }

  function unitOne(label) { return label ? label.replace(/s$/, '') : 'item'; }

  function addLine(line) {
    line.type = line.type || 'product';
    line.key = [line.type, line.id, line.variant || '', line.design ? 'd' : ''].join('|');
    const found = cart.find(i => i.key === line.key);
    if (found) found.qty += line.qty;
    else cart.push(line);
    saveCart(); renderCart();
  }

  function openCart(open) {
    if (!drawer) return;
    drawer.classList.toggle('open', open);
    drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.body.style.overflow = open ? 'hidden' : '';
  }
  $('#cartOpenBtn') && $('#cartOpenBtn').addEventListener('click', () => openCart(true));
  $$('[data-close-cart]').forEach(el => el.addEventListener('click', () => openCart(false)));

  // Delegated cart controls (+/- honour each line's step & MOQ)
  $('#cartItems') && $('#cartItems').addEventListener('click', e => {
    const inc = e.target.closest('[data-inc]'), dec = e.target.closest('[data-dec]'), rm = e.target.closest('[data-rm]');
    if (inc) { const it = cart[+inc.dataset.inc]; it.qty += (it.step || 1); }
    else if (dec) { const i = +dec.dataset.dec, it = cart[i]; it.qty -= (it.step || 1); if (it.qty < (it.moq || 1)) cart.splice(i, 1); }
    else if (rm) { cart.splice(+rm.dataset.rm, 1); }
    else return;
    saveCart(); renderCart();
  });
  $('#cartClearBtn') && $('#cartClearBtn').addEventListener('click', () => { cart = []; saveCart(); renderCart(); });

  // Simple add-to-cart buttons (fixed-price products AND packs)
  $$('[data-add-cart]').forEach(btn => btn.addEventListener('click', () => {
    addLine({
      type: btn.dataset.type || 'product',
      id: btn.dataset.id, name: btn.dataset.name, icon: btn.dataset.icon || 'bi-bag',
      unitType: 'fixed', unitLabel: '', variant: '',
      unitPrice: +btn.dataset.price, qty: 1, step: 1, moq: 1, design: 0, designFee: 0
    });
    flash(btn); openCart(true);
  }));

  // Product configurator (piece / meter products)
  const cfg = $('#productConfig');
  if (cfg) {
    const qtyEl = $('#cfgQty'), designEl = $('#cfgDesign'), totalEl = $('#cfgTotal');
    const moq = +cfg.dataset.moq || 1, step = +cfg.dataset.step || 1, designFee = +cfg.dataset.designFee || 0;
    function unitPrice() {
      const v = $('input[name="cfgVariant"]:checked');
      return v ? +v.value : (+cfg.dataset.basePrice || 0);
    }
    function recalc() {
      let q = parseInt(qtyEl.value, 10); if (isNaN(q) || q < moq) q = moq;
      q = Math.round(q / step) * step; if (q < moq) q = moq;
      const total = unitPrice() * q + (designEl && designEl.checked ? designFee : 0);
      totalEl.textContent = money(total);
      return q;
    }
    $('#cfgMinus') && $('#cfgMinus').addEventListener('click', () => { qtyEl.value = Math.max(moq, (parseInt(qtyEl.value,10)||moq) - step); recalc(); });
    $('#cfgPlus')  && $('#cfgPlus').addEventListener('click',  () => { qtyEl.value = (parseInt(qtyEl.value,10)||moq) + step; recalc(); });
    qtyEl.addEventListener('input', recalc);
    designEl && designEl.addEventListener('change', recalc);
    $$('input[name="cfgVariant"]').forEach(r => r.addEventListener('change', recalc));
    recalc();

    $('#cfgAdd').addEventListener('click', () => {
      const q = recalc(); qtyEl.value = q;
      const v = $('input[name="cfgVariant"]:checked');
      addLine({
        id: cfg.dataset.id, name: cfg.dataset.name, icon: cfg.dataset.icon || 'bi-bag',
        unitType: cfg.dataset.unitType, unitLabel: cfg.dataset.unitLabel,
        variant: v ? v.dataset.label : '',
        unitPrice: unitPrice(), qty: q, step: step, moq: moq,
        design: designEl && designEl.checked ? 1 : 0, designFee: designFee
      });
      flash($('#cfgAdd')); openCart(true);
    });
  }

  // Cart → checkout page (in-app + WhatsApp choice live there)
  $('#cartCheckoutBtn') && $('#cartCheckoutBtn').addEventListener('click', e => {
    e.preventDefault();
    if (!cart.length) return;
    window.location.href = (window.CHEAPA.siteUrl || '') + '/checkout.php';
  });

  function flash(btn) {
    const original = btn.innerHTML;
    btn.classList.add('add-flash');
    btn.innerHTML = '<i class="bi bi-check2"></i> Added';
    setTimeout(() => { btn.classList.remove('add-flash'); btn.innerHTML = original; }, 1400);
  }

  /* ---------- Checkout page ---------- */
  const coForm = $('#checkoutForm');
  if (coForm) {
    const empty = $('#checkoutEmpty');
    if (!cart.length) {
      empty && (empty.hidden = false);
    } else {
      coForm.hidden = false;
      const itemsEl = $('#checkoutItems');
      itemsEl.innerHTML = cart.map(it => `
        <div class="co-item">
          <span class="co-item-ico"><i class="bi ${it.icon || 'bi-bag'}"></i></span>
          <div class="co-item-main">
            <div class="co-item-name">${esc(it.name)}${it.variant ? ` · ${esc(it.variant)}` : ''}</div>
            <div class="co-item-sub">${it.qty}${it.unitLabel ? ' ' + esc(it.unitLabel) : ''} × ${money(it.unitPrice)}${it.design ? ` + design ${money(it.designFee)}` : ''}</div>
          </div>
          <div class="co-item-total">${money(lineTotal(it))}</div>
        </div>`).join('');

      const subtotal = cart.reduce((s, i) => s + i.unitPrice * i.qty, 0);
      const designTotal = cart.reduce((s, i) => s + (i.design ? i.designFee : 0), 0);
      const total = subtotal + designTotal;
      $('#coSubtotal').textContent = money(subtotal);
      if (designTotal > 0) { $('#coDesignLine').hidden = false; $('#coDesign').textContent = money(designTotal); }
      $('#coTotal').textContent = money(total);
      $('#coDeposit').textContent = money(Math.ceil(total / 2));

      // delivery address show/hide + fee label
      function deliveryUI() {
        const m = (document.querySelector('input[name="delivery_method"]:checked') || {}).value;
        $('#addrGroup').style.display = (m === 'delivery_kampala' || m === 'delivery_far') ? 'block' : 'none';
        $('#coDelivery').textContent = m === 'delivery_far' ? 'Confirmed on WhatsApp' : 'Free';
      }
      $$('input[name="delivery_method"]').forEach(r => r.addEventListener('change', deliveryUI));
      deliveryUI();

      // payload + channel
      $('#cartPayload').value = JSON.stringify(cart.map(i => ({ type: i.type || 'product', id: i.id, variant: i.variant || '', qty: i.qty, design: i.design ? 1 : 0 })));
      $('#placeInApp').addEventListener('click', () => { $('#channelField').value = 'in-app'; });
      $('#placeWa').addEventListener('click', () => { $('#channelField').value = 'whatsapp'; });

      // validate: WhatsApp required, and at least one of name / business name
      coForm.addEventListener('submit', e => {
        const name = ($('#coName').value || '').trim();
        const biz  = ($('#coBiz').value || '').trim();
        const wa   = ($('#coWa').value || '').trim();
        const err  = $('#coError');
        let msg = '';
        if (!wa) msg = 'Please enter your WhatsApp number.';
        else if (!name && !biz) msg = 'Please enter your name or your business name.';
        if (msg) {
          e.preventDefault();
          err.textContent = msg; err.hidden = false;
          err.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else { err.hidden = true; }
      });
    }
  }

  /* ---------- Admin: new-order bell ---------- */
  if (document.body.classList.contains('admin-body') && window.CHEAPA && window.CHEAPA.adminUrl) {
    const OKEY = 'cheapa_last_order_id';
    let audioCtx = null;
    const initAudio = () => { try { audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {} };
    document.addEventListener('click', initAudio, { once: true });

    function ding() {
      initAudio(); if (!audioCtx) return;
      if (audioCtx.state === 'suspended') audioCtx.resume();
      const t0 = audioCtx.currentTime;
      [880, 1320].forEach((freq, i) => {
        const o = audioCtx.createOscillator(), g = audioCtx.createGain();
        o.type = 'sine'; o.frequency.value = freq;
        o.connect(g); g.connect(audioCtx.destination);
        const t = t0 + i * 0.18;
        g.gain.setValueAtTime(0.0001, t);
        g.gain.exponentialRampToValueAtTime(0.35, t + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, t + 0.4);
        o.start(t); o.stop(t + 0.42);
      });
    }
    function orderToast(n) {
      let t = document.getElementById('orderToast');
      if (!t) {
        t = document.createElement('a');
        t.id = 'orderToast'; t.className = 'order-toast';
        t.href = window.CHEAPA.adminUrl + '/orders.php';
        document.body.appendChild(t);
      }
      t.innerHTML = '<i class="bi bi-bag-check-fill"></i> <span>' + (n > 1 ? n + ' new orders!' : 'New order received!') + '</span> <i class="bi bi-arrow-right"></i>';
      t.classList.add('show');
      clearTimeout(t._timer); t._timer = setTimeout(() => t.classList.remove('show'), 9000);
    }
    async function pollOrders() {
      try {
        const r = await fetch(window.CHEAPA.adminUrl + '/notify.php', { credentials: 'same-origin' });
        if (!r.ok) return;
        const d = await r.json();
        const last = parseInt(localStorage.getItem(OKEY) || '0', 10);
        if (!last) { localStorage.setItem(OKEY, String(d.latest)); return; } // first run: don't alert
        if (d.latest > last) { localStorage.setItem(OKEY, String(d.latest)); ding(); orderToast(d.latest - last); }
      } catch (e) {}
    }
    pollOrders();
    setInterval(pollOrders, 20000);
  }

  renderCart();

  /* ============================================================
     SHOP — search + category filter
     ============================================================ */
  const search = $('#shopSearch'), chips = $$('.filter-chip'), noRes = $('#shopNoResults');
  function applyShop() {
    const term = (search ? search.value : '').toLowerCase().trim();
    const active = $('.filter-chip.active');
    const cat = active ? active.dataset.cat : 'all';
    let shown = 0;
    $$('.product-card').forEach(card => {
      const hay = (card.dataset.search || card.dataset.name || '').toLowerCase();
      const matchCat = cat === 'all' || card.dataset.cat === cat;
      const matchTerm = !term || hay.includes(term);
      const show = matchCat && matchTerm;
      card.classList.toggle('hidden', !show);
      if (show) shown++;
    });
    if (noRes) noRes.hidden = shown !== 0;
  }
  chips.forEach(c => c.addEventListener('click', () => {
    chips.forEach(x => x.classList.remove('active')); c.classList.add('active'); applyShop();
  }));
  if (search) {
    search.addEventListener('input', applyShop);
    search.addEventListener('search', applyShop); // clears via the "x"
  }

  /* ============================================================
     PACKS — business stage selector
     ============================================================ */
  const stageChips = $$('.stage-chip');
  stageChips.forEach(c => c.addEventListener('click', () => {
    stageChips.forEach(x => x.classList.remove('active')); c.classList.add('active');
    const stage = c.dataset.stage;
    $$('.pack-card').forEach(card => {
      card.style.display = (stage === 'all' || card.dataset.stage === stage) ? 'flex' : 'none';
    });
  }));

  /* ============================================================
     CHATBOT — button-first sales assistant
     ============================================================ */
  const fab = $('#chatFab'), widget = $('#chatWidget'),
        body = $('#chatBody'), quick = $('#chatQuick');
  const url = (window.CHEAPA && window.CHEAPA.siteUrl) || '';

  const FLOW = {
    start: {
      bot: ["Hi 👋 Welcome to Cheapa Studio! I'm here to help you grow your brand. What are you looking for?"],
      options: [
        { t: '📦 Business Packs', go: 'packs' },
        { t: '🛍️ Design Shop', go: 'shop' },
        { t: '🌐 Web Design', go: 'web' },
        { t: "❓ I'm not sure", go: 'unsure' }
      ]
    },
    packs: {
      bot: ["Great choice! Our Business Growth Packs bundle everything a business needs — and save you money:",
            "• Launch Pack — UGX 100,000\n• Visibility Pack — UGX 150,000\n• Growth Pack — UGX 500,000 ⭐\n• Authority Pack — UGX 1,000,000"],
      options: [
        { t: 'See all packs', href: url + '/packs.php' },
        { t: 'Which fits me?', go: 'unsure' },
        { t: 'Talk to a human', wa: 'Hi! I want help choosing a Business Growth Pack.' }
      ]
    },
    shop: {
      bot: ["Our Design Shop has individual products you can order fast — logos, business cards, flyers, posters, social posts and more, starting from UGX 25,000."],
      options: [
        { t: 'Open the Shop', href: url + '/shop.php' },
        { t: 'Order on WhatsApp', wa: 'Hi! I want to order a design from the shop.' }
      ]
    },
    web: {
      bot: ["We build mobile-friendly business websites, landing pages and portfolio sites. Most projects start with a 50% deposit and we deliver fast."],
      options: [
        { t: 'Web design services', href: url + '/services.php' },
        { t: 'Request a website', wa: 'Hi! I want a website for my business.' }
      ]
    },
    unsure: {
      bot: ["No problem — let's find your fit. What stage is your business at?"],
      options: [
        { t: 'Just starting', go: 'rec_start' },
        { t: 'Growing', go: 'rec_grow' },
        { t: 'Established', go: 'rec_estab' }
      ]
    },
    rec_start: {
      bot: ["Perfect 🚀 For a new business, the **Launch Pack (UGX 100,000)** gives you a logo, business cards, flyers and WhatsApp branding to open your doors."],
      options: [
        { t: 'See the Launch Pack', href: url + '/packs.php' },
        { t: 'Start on WhatsApp', wa: 'Hi! I want the Launch Pack for my new business.' }
      ]
    },
    rec_grow: {
      bot: ["Nice 📈 To get noticed, the **Visibility Pack (UGX 150,000)** adds banners, more print and social posts on top of your branding."],
      options: [
        { t: 'See the Visibility Pack', href: url + '/packs.php' },
        { t: 'Start on WhatsApp', wa: 'Hi! I want the Visibility Pack.' }
      ]
    },
    rec_estab: {
      bot: ["Excellent 🏆 The **Growth Pack (UGX 500,000)** is our most popular — full branding, company profile, a website and Google Business setup."],
      options: [
        { t: 'See the Growth Pack', href: url + '/packs.php' },
        { t: 'Start on WhatsApp', wa: 'Hi! I want the Growth Pack.' }
      ]
    }
  };

  function botSay(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg bot';
    div.innerHTML = esc(text).replace(/\n/g, '<br>').replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    body.appendChild(div); body.scrollTop = body.scrollHeight;
  }
  function userSay(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg user'; div.textContent = text;
    body.appendChild(div); body.scrollTop = body.scrollHeight;
  }
  function renderNode(key) {
    const node = FLOW[key]; if (!node) return;
    node.bot.forEach((t, i) => setTimeout(() => botSay(t), 220 * (i + 1)));
    setTimeout(() => {
      quick.innerHTML = '';
      node.options.forEach(opt => {
        const b = document.createElement('button');
        b.textContent = opt.t;
        b.addEventListener('click', () => {
          userSay(opt.t);
          if (opt.href) { window.location.href = opt.href; return; }
          if (opt.wa) { window.open(`https://wa.me/${WA}?text=${encodeURIComponent(opt.wa)}`, '_blank'); return; }
          if (opt.go) { quick.innerHTML = ''; renderNode(opt.go); }
        });
        quick.appendChild(b);
      });
    }, 220 * (node.bot.length + 1));
  }

  let chatStarted = false;
  function openChat(open) {
    if (!widget) return;
    widget.classList.toggle('open', open);
    widget.setAttribute('aria-hidden', open ? 'false' : 'true');
    fab && fab.classList.toggle('hidden', open);
    if (open && !chatStarted) { chatStarted = true; renderNode('start'); }
  }
  fab && fab.addEventListener('click', () => openChat(true));
  $('#chatOpenBtnBottom') && $('#chatOpenBtnBottom').addEventListener('click', () => openChat(true));
  $('#chatCloseBtn') && $('#chatCloseBtn').addEventListener('click', () => openChat(false));

  /* ---------- password toggle (admin) ---------- */
  $$('.pw-toggle').forEach(t => t.addEventListener('click', () => {
    const inp = t.parentElement.querySelector('input');
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
    t.querySelector('i').className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
  }));

  /* ---------- Share ---------- */
  function buildShareSheet() {
    let el = $('#shareSheet');
    if (el) return el;
    el = document.createElement('div');
    el.id = 'shareSheet'; el.className = 'share-sheet';
    el.innerHTML = `
      <div class="share-backdrop" data-share-close></div>
      <div class="share-panel" role="dialog" aria-modal="true" aria-label="Share">
        <div class="share-head"><strong>Share this</strong><button class="icon-btn" data-share-close aria-label="Close"><i class="bi bi-x-lg"></i></button></div>
        <div class="share-options">
          <a class="share-opt" data-net="whatsapp" target="_blank" rel="noopener"><span class="share-ic" style="background:#25D366"><i class="bi bi-whatsapp"></i></span>WhatsApp</a>
          <a class="share-opt" data-net="facebook" target="_blank" rel="noopener"><span class="share-ic" style="background:#1877F2"><i class="bi bi-facebook"></i></span>Facebook</a>
          <a class="share-opt" data-net="x" target="_blank" rel="noopener"><span class="share-ic" style="background:#111"><i class="bi bi-twitter-x"></i></span>X</a>
          <a class="share-opt" data-net="telegram" target="_blank" rel="noopener"><span class="share-ic" style="background:#229ED9"><i class="bi bi-telegram"></i></span>Telegram</a>
          <button class="share-opt" data-net="copy" type="button"><span class="share-ic" style="background:var(--violet)"><i class="bi bi-link-45deg"></i></span><span data-copy-label>Copy link</span></button>
        </div>
      </div>`;
    document.body.appendChild(el);
    el.querySelectorAll('[data-share-close]').forEach(b => b.addEventListener('click', () => el.classList.remove('open')));
    return el;
  }
  function openShareSheet(url, title) {
    const sheet = buildShareSheet();
    const enc = encodeURIComponent(url), et = encodeURIComponent(title);
    sheet.querySelector('[data-net="whatsapp"]').href = `https://wa.me/?text=${et}%20${enc}`;
    sheet.querySelector('[data-net="facebook"]').href = `https://www.facebook.com/sharer/sharer.php?u=${enc}`;
    sheet.querySelector('[data-net="x"]').href        = `https://twitter.com/intent/tweet?text=${et}&url=${enc}`;
    sheet.querySelector('[data-net="telegram"]').href = `https://t.me/share/url?url=${enc}&text=${et}`;
    sheet.querySelector('[data-net="copy"]').onclick = () => {
      if (navigator.clipboard) navigator.clipboard.writeText(url).then(() => {
        const l = sheet.querySelector('[data-copy-label]'); if (l) { l.textContent = 'Copied!'; setTimeout(() => l.textContent = 'Copy link', 1500); }
      });
    };
    sheet.classList.add('open');
  }
  document.addEventListener('click', e => {
    const btn = e.target.closest('.share-btn');
    if (!btn) return;
    e.preventDefault(); e.stopPropagation();
    const url = btn.dataset.shareUrl, title = btn.dataset.shareTitle || document.title;
    if (navigator.share) { navigator.share({ title, url }).catch(() => {}); return; }
    openShareSheet(url, title);
  });

  /* ---------- util ---------- */
  function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
})();
