<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('k007') ?> — TS Palletium</title>
<meta name="description" content="<?= t('k046') ?>">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css">
<?= hreflangs('produkty.html') ?>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a href="index.html" class="brand" aria-label="<?= t('k003') ?>">
      <img src="/assets/img/logo.png" alt="TS Palletium — Built on Trust">
    </a>
    <nav class="main-nav" aria-label="<?= t('k002') ?>">
      <a href="index.html"><?= t('k001') ?></a>
      <a href="o-spolocnosti.html"><?= t('k009') ?></a>
      <a href="produkty.html" class="active"><?= t('k007') ?></a>
      <a href="kontakt.html"><?= t('k178') ?></a>
      <a href="dopyt.html" class="mobile-cta"><?= t('k006') ?></a>
    </nav>
    <?= lang_switcher('produkty.html') ?>
    <div class="header-cta">
      <a href="dopyt.html" class="btn btn-gold"><?= t('k006') ?></a>
    </div>
    <button class="nav-toggle" aria-label="<?= t('k004') ?>" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>

  <section class="page-hero">
    <div class="bg" style="background-image:url('/assets/img/warehouse-aisles.jpg');"></div>
    <div class="container">
      <div class="breadcrumb"><a href="index.html"><?= t('k001') ?></a> / <?= t('k007') ?></div>
      <h1><?= t('k014') ?></h1>
      <p><?= t('k030') ?></p>
    </div>
  </section>

  <!-- PRODUKTY GRID -->
  <section class="section">
    <div class="container">
      <div class="products-grid">
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/product-epal.jpg" alt="<?= t('k043') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k032') ?></span>
            <h3><?= t('k035') ?></h3>
            <p><?= t('k047') ?></p>
          </div>
        </article>
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/pallets-grid.jpg" alt="<?= t('k066') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k048') ?></span>
            <h3><?= t('k038') ?></h3>
            <p><?= t('k049') ?></p>
          </div>
        </article>
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/product-h1.jpg" alt="<?= t('k044') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k033') ?></span>
            <h3><?= t('k036') ?></h3>
            <p><?= t('k050') ?></p>
          </div>
        </article>
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/epal-warehouse.jpg" alt="<?= t('k067') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k052') ?></span>
            <h3><?= t('k051') ?></h3>
            <p><?= t('k053') ?></p>
          </div>
        </article>
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/epal-stacks-closeup.jpg" alt="<?= t('k068') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag">Export</span>
            <h3><?= t('k054') ?></h3>
            <p><?= t('k055') ?></p>
          </div>
        </article>
        <article class="prod-card dark reveal">
          <div class="thumb"><img src="/assets/img/pallets-sky.jpg" alt="<?= t('k021') ?>" loading="lazy"></div>
          <div class="body">
            <span class="tag"><?= t('k022') ?></span>
            <h3><?= t('k056') ?></h3>
            <p><?= t('k020') ?></p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- INDIVIDUÁLNY PRÍSTUP -->
  <section class="section dark-2">
    <div class="container">
      <div class="split reveal">
        <div class="media">
          <img src="/assets/img/forklift-warehouse.jpg" alt="<?= t('k069') ?>" loading="lazy">
        </div>
        <div>
          <h2><?= t('k057') ?></h2>
          <div class="title-rule left"></div>
          <p><?= t('k058') ?></p>
          <ul class="checklist single">
            <li><?= t('k059') ?></li>
            <li><?= t('k060') ?></li>
            <li><?= t('k061') ?></li>
            <li><?= t('k062') ?></li>
          </ul>
          <p style="margin-top:26px;"><a href="dopyt.html" class="btn btn-gold"><?= t('k006') ?> <span class="arr">→</span></a></p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-band">
    <div class="container">
      <h2><?= t('k063') ?></h2>
      <p><?= t('k064') ?></p>
      <a href="kontakt.html" class="btn"><?= t('k065') ?> <span class="arr">→</span></a>
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
</body>
</html>
