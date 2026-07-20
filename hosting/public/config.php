<?php
// TS Palletium — site configuration
const BASE_URL = 'https://tspalletium.com';   // canonical domain, no trailing slash
const DB_PATH  = __DIR__ . '/data/site.db';
const SEED_PATH = __DIR__ . '/seed/data.json';
const LANGS = ['sk', 'cs', 'en', 'de'];
const DEFAULT_LANG = 'sk';
const MAIN_PAGES = ['index.html', 'o-spolocnosti.html', 'produkty.html', 'dopyt.html', 'kontakt.html'];
const LEGAL_SLUGS = ['cookies', 'gdpr', 'obchodne-podmienky', 'pravne-informacie'];
