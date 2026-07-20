# TS Palletium — hosting verze (PHP + admin)

PHP 8 aplikace pro WebSupport: vícejazyčný web (SK/CS/EN/DE) s administrací.
Statická verze v kořenu repa slouží jen jako náhled na GitHub Pages — ostrá verze je tato.

## Struktura

```
hosting/
  public/            <- WEB ROOT, nahrává se na hosting (u WebSupportu do /web/)
    index.php        <- router veřejného webu (jazyky, tracking návštěv)
    form-submit.php  <- endpoint poptávkového formuláře (ukládá + e-mail + autoodpověď)
    lib.php          <- DB (SQLite), seed, překlady, helpery
    config.php       <- BASE_URL, cesty
    .htaccess        <- HTTPS + www redirect, routing, cache, ochrana složek
    templates/       <- PHP šablony stránek (texty tahané z DB přes t('klíč'))
    seed/data.json   <- výchozí obsah DB (texty 4 jazyky, právní dokumenty, nastavení)
    seed/initial-password.txt  <- VYTVOŘIT PŘI NASAZENÍ, není v gitu (viz níže)
    data/            <- SQLite DB (vznikne sama při prvním requestu)
    admin/           <- administrace (login, texty, právní dokumenty, fotky, dopyty, nastavení)
    assets/ css/ js/ <- statické soubory
  dev-router.php     <- jen pro lokální vývoj (php -S)
  seed/              <- zdrojové překlady právních dokumentů (generují se z nich data.json)
```

## Lokální spuštění

```
php -S localhost:8742 -t public dev-router.php
```
(potřeba PHP 8.1+ s rozšířeními pdo_sqlite, gd, mbstring)

## Nasazení na WebSupport

1. Nahrát obsah `hosting/public/` do web rootu domény (FTP/SSH).
2. Na serveru vytvořit `seed/initial-password.txt` s jednorázovým admin heslem
   (soubor se smaže sám po prvním přihlášení; heslo si admin změní v Nastaveniach).
3. První otevření webu založí a naplní databázi `data/site.db` ze `seed/data.json`.
4. V administraci WebSupportu: doména -> webhosting, zapnout Let's Encrypt SSL.
5. Ověřit: všechny stránky, /admin/ login, testovací poptávka (e-mail dorazí na
   adresu v Nastaveniach — odesílatel musí být schránka na doméně, např. info@).

## Poznámky

- DB i nahrané fotky žijí jen na serveru — zálohovat přes admin (Nastavenia -> Záloha).
- Statistiky se měří server-side bez cookies (žádný vliv na cookie lištu).
- Kanonická doména: https://tspalletium.com (bez www) — viz .htaccess a config.php.
- Fotky se při uploadu zmenšují na max 1920 px, nikdy se nezvětšují.
