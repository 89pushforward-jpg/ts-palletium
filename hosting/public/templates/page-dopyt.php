<!DOCTYPE html>
<html lang="<?= $LANG ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('k005') ?> — TS Palletium</title>
<meta name="description" content="<?= t('k136') ?>">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css">
<?= hreflangs('dopyt.html') ?>
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
      <a href="produkty.html"><?= t('k007') ?></a>
      <a href="kontakt.html"><?= t('k178') ?></a>
      <a href="dopyt.html" class="mobile-cta active"><?= t('k006') ?></a>
    </nav>
    <?= lang_switcher('dopyt.html') ?>
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
    <div class="bg" style="background-image:url('/assets/img/epal-warehouse.jpg');"></div>
    <div class="container">
      <div class="breadcrumb"><a href="index.html"><?= t('k001') ?></a> / <?= t('k006') ?></div>
      <h1><?= t('k137') ?></h1>
      <p><?= t('k138') ?></p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="contact-grid">
        <div>
          <div class="contact-card reveal">
            <h3><?= t('k139') ?></h3>
            <div class="contact-row">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M8 12h8M8 8h8M8 16h5"/><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
              <div><span class="lbl"><?= t('k140') ?></span><span class="val"><?= t('k143') ?></span></div>
            </div>
            <div class="contact-row">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 5l4-1 2 5-2 1a12 12 0 0 0 6 6l1-2 5 2-1 4c-9 1-16-6-15-15z"/></svg>
              <div><span class="lbl"><?= t('k141') ?></span><span class="val"><?= t('k144') ?></span></div>
            </div>
            <div class="contact-row">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M3 7h11v10H3z"/><path d="M14 10h4l3 3v4h-7"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
              <div><span class="lbl"><?= t('k142') ?></span><span class="val"><?= t('k145') ?></span></div>
            </div>
          </div>
          <div class="contact-card reveal" style="margin-top:24px;">
            <h3><?= t('k146') ?></h3>
            <div class="contact-row">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 5l4-1 2 5-2 1a12 12 0 0 0 6 6l1-2 5 2-1 4c-9 1-16-6-15-15z"/></svg>
              <div><span class="lbl"><?= t('k171') ?></span><a href="tel:+420704222545">+420 704 222 545</a></div>
            </div>
            <div class="contact-row">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
              <div><span class="lbl">E-mail</span><a href="mailto:info@tspalletium.com">info@tspalletium.com</a></div>
            </div>
          </div>
        </div>

        <div class="form-card reveal">
          <h2 style="font-size:22px; text-transform:uppercase; margin-bottom:6px;"><?= t('k147') ?></h2>
          <div class="title-rule left"></div>
          <form id="quoteForm" novalidate>
            <div class="form-grid">
              <div class="form-field">
                <label for="fName"><?= t('k148') ?> <span class="req">*</span></label>
                <input type="text" id="fName" name="name" required autocomplete="name">
              </div>
              <div class="form-field">
                <label for="fCompany"><?= t('k149') ?> <span class="req">*</span></label>
                <input type="text" id="fCompany" name="company" required autocomplete="organization">
              </div>
              <div class="form-field">
                <label for="fEmail">E-mail <span class="req">*</span></label>
                <input type="email" id="fEmail" name="email" required autocomplete="email">
              </div>
              <div class="form-field">
                <label for="fPhone"><?= t('k171') ?></label>
                <input type="tel" id="fPhone" name="phone" autocomplete="tel">
              </div>
              <div class="form-field">
                <label for="fType"><?= t('k150') ?> <span class="req">*</span></label>
                <select id="fType" name="type" required>
                  <option value=""><?= t('k154') ?></option>
                  <option><?= t('k155') ?></option>
                  <option><?= t('k156') ?></option>
                  <option><?= t('k157') ?></option>
                  <option><?= t('k158') ?></option>
                  <option><?= t('k067') ?></option>
                  <option><?= t('k068') ?></option>
                  <option><?= t('k159') ?></option>
                  <option><?= t('k160') ?></option>
                </select>
              </div>
              <div class="form-field">
                <label for="fQty"><?= t('k151') ?></label>
                <input type="text" id="fQty" name="qty" placeholder="<?= t('k161') ?>">
              </div>
              <div class="form-field full">
                <label for="fPlace"><?= t('k152') ?></label>
                <input type="text" id="fPlace" name="place" placeholder="<?= t('k162') ?>">
              </div>
              <div class="form-field full">
                <label for="fMsg"><?= t('k153') ?></label>
                <textarea id="fMsg" name="message" placeholder="<?= t('k163') ?>"></textarea>
              </div>
            </div>
            <p style="margin-top:24px;">
              <button type="submit" class="btn btn-gold"><?= t('k164') ?> <span class="arr">→</span></button>
            </p>
            <p class="form-note"><?= t('k165') ?></p>
            <div class="form-status" id="formStatus"><?= t('k166') ?></div>
          </form>
        </div>
      </div>
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

<script>window.TSP_API='/form-submit.php';window.TSP_LANG='<?= $LANG ?>';</script>
<script src="/js/main.js"></script>
<?= view_beacon() ?>
</body>
</html>
