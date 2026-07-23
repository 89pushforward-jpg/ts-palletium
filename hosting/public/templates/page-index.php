<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('k010') ?></title>
<meta name="description" content="<?= t('k011') ?>">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css">
<?= hreflangs('index.html') ?>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a href="index.html" class="brand" aria-label="<?= t('k003') ?>">
      <img src="/assets/img/logo.png" alt="TS Palletium — Built on Trust">
    </a>
    <nav class="main-nav" aria-label="<?= t('k002') ?>">
      <a href="index.html" class="active"><?= t('k001') ?></a>
      <a href="o-spolocnosti.html"><?= t('k009') ?></a>
      <a href="produkty.html"><?= t('k007') ?></a>
      <a href="kontakt.html"><?= t('k178') ?></a>
      <a href="dopyt.html" class="mobile-cta"><?= t('k006') ?></a>
    </nav>
    <?= lang_switcher('index.html') ?>
    <div class="header-cta">
      <a href="dopyt.html" class="btn btn-gold"><?= t('k006') ?></a>
    </div>
    <button class="nav-toggle" aria-label="<?= t('k004') ?>" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <div class="hero-grid">
        <div class="hero-copy">
          <h1><?= t('k012') ?></h1>
          <p class="sub"><?= t('k013') ?></p>
          <div class="hero-actions">
            <a href="produkty.html" class="btn btn-gold"><?= t('k015') ?> <span class="arr">→</span></a>
            <a href="dopyt.html" class="btn btn-outline"><?= t('k006') ?> <span class="arr">→</span></a>
          </div>
          <div class="hero-badges">
            <div class="badge">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 3l7 3v5c0 4.5-3 8.4-7 10-4-1.6-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>
              <span><?= t('k016') ?></span>
            </div>
            <div class="badge">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
              <span><?= t('k017') ?></span>
            </div>
            <div class="badge">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 4 5.5 4 9s-1.5 6.5-4 9c-2.5-2.5-4-5.5-4-9s1.5-6.5 4-9z"/></svg>
              <span><?= t('k018') ?></span>
            </div>
          </div>
        </div>
        <div class="hero-media">
          <img src="/assets/img/hero-building-tall.jpg" alt="<?= t('k019') ?>" fetchpriority="high">
        </div>
      </div>
    </div>
  </section>

  <!-- STATS STRIP -->
  <div class="stats-strip">
    <div class="container stats-grid">
      <div class="stat">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3 16h18M3 20h18M5 16v-4h4v4M10 16v-4h4v4M15 16v-4h4v4M4 12h16M6 8h12l1 4H5l1-4z"/></svg>
        <div><span class="num"><?= t('k022') ?></span><span class="lbl"><?= t('k023') ?></span></div>
      </div>
      <div class="stat">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 4 5.5 4 9s-1.5 6.5-4 9c-2.5-2.5-4-5.5-4-9s1.5-6.5 4-9z"/></svg>
        <div><span class="num"><?= t('k024') ?></span><span class="lbl"><?= t('k025') ?></span></div>
      </div>
      <div class="stat">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M8 12h8M8 8h8M8 16h5"/><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
        <div><span class="num"><?= t('k026') ?></span><span class="lbl"><?= t('k027') ?></span></div>
      </div>
      <div class="stat">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M7 11l3 3 7-7"/><path d="M12 21a9 9 0 1 1 9-9"/><path d="M17 17l4 4M21 17v4h-4"/></svg>
        <div><span class="num"><?= t('k028') ?></span><span class="lbl"><?= t('k029') ?></span></div>
      </div>
    </div>
  </div>

  <!-- PRODUKTY -->
  <section class="section light">
    <div class="container">
      <h2 class="section-title reveal"><?= t('k014') ?></h2>
      <div class="title-rule"></div>
      <p class="section-lead reveal"><?= t('k031') ?></p>
      <div class="products-grid">
        <article class="prod-card reveal">
          <div class="thumb"><img src="/assets/img/product-epal.jpg" alt="<?= t('k043') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k032') ?></span>
            <h3><?= t('k035') ?></h3>
            <p><?= t('k039') ?></p>
          </div>
        </article>
        <article class="prod-card reveal">
          <div class="thumb"><img src="/assets/img/product-h1.jpg" alt="<?= t('k044') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k033') ?></span>
            <h3><?= t('k036') ?></h3>
            <p><?= t('k040') ?></p>
          </div>
        </article>
        <article class="prod-card reveal">
          <div class="thumb"><img src="/assets/img/pallets-grid.jpg" alt="<?= t('k045') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k034') ?></span>
            <h3><?= t('k037') ?></h3>
            <p><?= t('k041') ?></p>
          </div>
        </article>
      </div>
      <p style="text-align:center; margin-top:44px;" class="reveal">
        <a href="produkty.html" class="btn btn-gold"><?= t('k042') ?> <span class="arr">→</span></a>
      </p>
    </div>
  </section>

  <!-- HLAVNÉ ČINNOSTI -->
  <section class="section dark-2">
    <div class="container">
      <h2 class="section-title reveal"><?= t('k070') ?></h2>
      <div class="title-rule"></div>
      <p class="section-lead reveal"><?= t('k071') ?></p>
      <div class="cards-3">
        <article class="svc-card reveal">
          <div class="thumb"><img src="/assets/img/epal-stacks-closeup.jpg" alt="<?= t('k072') ?>" loading="lazy"></div>
          <div class="body">
            <div class="svc-num">01</div>
            <h3><?= t('k073') ?></h3>
            <p><?= t('k074') ?></p>
          </div>
        </article>
        <article class="svc-card reveal">
          <div class="thumb"><img src="/assets/img/epal-warehouse.jpg" alt="<?= t('k075') ?>" loading="lazy"></div>
          <div class="body">
            <div class="svc-num">02</div>
            <h3><?= t('k076') ?></h3>
            <p><?= t('k077') ?></p>
          </div>
        </article>
        <article class="svc-card reveal">
          <div class="thumb"><img src="/assets/img/forklift-warehouse.jpg" alt="<?= t('k078') ?>" loading="lazy"></div>
          <div class="body">
            <div class="svc-num">03</div>
            <h3><?= t('k079') ?></h3>
            <p><?= t('k080') ?></p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- NÁŠ PRÍSTUP / PREČO -->
  <section class="section">
    <div class="container">
      <div class="split reveal">
        <div class="media">
          <img src="/assets/img/pallets-sky.jpg" alt="<?= t('k093') ?>" loading="lazy">
        </div>
        <div>
          <h2><?= t('k081') ?></h2>
          <div class="title-rule left"></div>
          <p><?= t('k082') ?></p>
          <ul class="checklist">
            <li><?= t('k084') ?></li>
            <li><?= t('k085') ?></li>
            <li><?= t('k086') ?></li>
            <li><?= t('k087') ?></li>
            <li><?= t('k088') ?></li>
            <li><?= t('k089') ?></li>
            <li><?= t('k090') ?></li>
            <li><?= t('k092') ?></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- O SPOLOČNOSTI -->
  <section class="section dark-2">
    <div class="container">
      <div class="split reveal">
        <div>
          <h2><?= t('k008') ?></h2>
          <div class="title-rule left"></div>
          <p><?= t('k094') ?></p>
          <p><?= t('k096') ?></p>
          <p><strong><?= t('k098') ?></strong></p>
          <p style="margin-top:26px;"><a href="o-spolocnosti.html" class="btn btn-outline"><?= t('k099') ?> <span class="arr">→</span></a></p>
        </div>
        <div class="media">
          <img src="/assets/img/warehouse-aisles.jpg" alt="<?= t('k100') ?>" loading="lazy">
        </div>
      </div>
    </div>
  </section>

  <!-- VÍZIA BAND -->
  <section class="band" style="background-image:url('/assets/img/collage-warehouse.jpg');">
    <div class="container">
      <h2 class="reveal"><?= t('k112') ?></h2>
      <p class="reveal"><?= t('k114') ?></p>
      <p class="reveal"><?= t('k116') ?></p>
      <p style="margin-top:28px;" class="reveal"><a href="o-spolocnosti.html#vizia" class="btn btn-outline"><?= t('k118') ?> <span class="arr">→</span></a></p>
    </div>
  </section>

  <!-- SPOLUPRÁCA -->
  <section class="section dark-2">
    <div class="container">
      <h2 class="section-title reveal"><?= t('k119') ?></h2>
      <div class="title-rule"></div>
      <p class="section-lead reveal"><?= t('k120') ?></p>
      <div class="cards-3">
        <article class="svc-card reveal">
          <div class="body" style="text-align:center;">
            <h3 style="margin-top:8px;"><?= t('k123') ?></h3>
            <p><?= t('k124') ?></p>
          </div>
        </article>
        <article class="svc-card reveal">
          <div class="body" style="text-align:center;">
            <h3 style="margin-top:8px;"><?= t('k125') ?></h3>
            <p><?= t('k126') ?></p>
          </div>
        </article>
        <article class="svc-card reveal">
          <div class="body" style="text-align:center;">
            <h3 style="margin-top:8px;"><?= t('k127') ?></h3>
            <p><?= t('k128') ?></p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-band">
    <div class="container">
      <h2><?= t('k130') ?></h2>
      <p><?= t('k131') ?></p>
      <a href="dopyt.html" class="btn"><?= t('k005') ?> <span class="arr">→</span></a>
    </div>
  </section>

</main>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="/assets/img/logo-kruh.png" alt="TS Palletium — Built on Trust">
        <p><?= t('k179') ?></p>
      </div>
      <div>
        <h4><?= t('k180') ?></h4>
        <ul class="footer-links">
          <li><a href="index.html"><?= t('k001') ?></a></li>
          <li><a href="o-spolocnosti.html"><?= t('k009') ?></a></li>
          <li><a href="produkty.html"><?= t('k007') ?></a></li>
          <li><a href="dopyt.html"><?= t('k006') ?></a></li>
          <li><a href="kontakt.html"><?= t('k178') ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?= t('k181') ?></h4>
        <ul class="footer-links">
          <li><a href="obchodne-podmienky.html"><?= t('k182') ?></a></li>
          <li><a href="gdpr.html"><?= t('k183') ?></a></li>
          <li><a href="cookies.html"><?= t('k184') ?></a></li>
          <li><a href="pravne-informacie.html"><?= t('k181') ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?= t('k178') ?></h4>
        <ul class="footer-contact">
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            <span>Tom Kave Company s. r. o.<br>Južná trieda 2881/4B<br><?= t('k185') ?></span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 5l4-1 2 5-2 1a12 12 0 0 0 6 6l1-2 5 2-1 4c-9 1-16-6-15-15z"/></svg>
            <a href="tel:+420704222545">+420 704 222 545</a>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            <a href="mailto:info@tspalletium.com">info@tspalletium.com</a>
          </li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span><?= t('k186') ?></span>
      <span><?= t('k187') ?></span>
    </div>
  </div>
</footer>

<!-- COOKIE BAR -->
<div class="cookie-bar" id="cookieBar" role="dialog" aria-label="<?= t('k193') ?>">
  <h4><?= t('k189') ?></h4>
  <p><?= t('k190') ?></p>
  <div class="cookie-actions">
    <button class="btn btn-gold" id="cookieAcceptAll"><?= t('k191') ?></button>
    <button class="btn btn-outline" id="cookieNecessary"><?= t('k192') ?></button>
  </div>
</div>

<script src="/js/main.js"></script>
<?= view_beacon() ?>
</body>
</html>
