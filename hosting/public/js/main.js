/* TS Palletium — shared behaviour */
(function () {
  'use strict';

  /* Mobile navigation */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
      toggle.classList.toggle('open');
      toggle.setAttribute('aria-expanded', nav.classList.contains('open') ? 'true' : 'false');
    });
  }

  /* Scroll reveal */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('visible'); });
  }

  /* Cookie bar — consent stored in localStorage */
  var KEY = 'tsp-cookie-consent';
  var bar = document.getElementById('cookieBar');
  if (bar && !localStorage.getItem(KEY)) {
    bar.classList.add('show');
  }
  function consent(value) {
    localStorage.setItem(KEY, value);
    if (bar) bar.classList.remove('show');
    /* Analytics tools would be initialised here only when value === 'all' */
  }
  var btnAll = document.getElementById('cookieAcceptAll');
  var btnNec = document.getElementById('cookieNecessary');
  if (btnAll) btnAll.addEventListener('click', function () { consent('all'); });
  if (btnNec) btnNec.addEventListener('click', function () { consent('necessary'); });

  /* Quote form — on PHP hosting (window.TSP_API) posts to the backend,
     otherwise builds a prefilled e-mail (static preview without backend) */
  var form = document.getElementById('quoteForm');
  if (form && window.TSP_API) {
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var btn = form.querySelector('button[type="submit"]');
      if (btn) btn.disabled = true;
      var data = new FormData(form);
      data.append('lang', window.TSP_LANG || 'sk');
      fetch(window.TSP_API, { method: 'POST', body: data })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          var status = document.getElementById('formStatus');
          if (status) {
            status.textContent = res.message || '';
            status.classList.add('show');
          }
          if (res.ok) form.reset();
        })
        .catch(function () {
          var status = document.getElementById('formStatus');
          if (status) {
            status.textContent = 'Error — please write to info@tspalletium.com';
            status.classList.add('show');
          }
        })
        .finally(function () { if (btn) btn.disabled = false; });
    });
  } else if (form) {
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var get = function (id) { var el = form.querySelector('#' + id); return el ? el.value.trim() : ''; };
      var lines = [
        'Dobrý deň,',
        '',
        'mám záujem o cenovú ponuku:',
        '',
        'Meno a priezvisko: ' + get('fName'),
        'Spoločnosť: ' + get('fCompany'),
        'E-mail: ' + get('fEmail'),
        'Telefón: ' + get('fPhone'),
        'Typ paliet: ' + get('fType'),
        'Množstvo: ' + get('fQty'),
        'Miesto dodania: ' + get('fPlace'),
        '',
        'Správa:',
        get('fMsg')
      ];
      var mailto = 'mailto:info@tspalletium.com'
        + '?subject=' + encodeURIComponent('Dopyt z webu — ' + (get('fCompany') || get('fName')))
        + '&body=' + encodeURIComponent(lines.join('\n'));
      window.location.href = mailto;
      var status = document.getElementById('formStatus');
      if (status) status.classList.add('show');
    });
  }
})();
