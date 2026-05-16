# automatizovany-dum.cz - statická kopie

Statická kopie webu `https://www.automatizovany-dum.cz/` připravená k samostatnému nasazení (bez měsíčního poplatku původnímu provozovateli).

## Co je v balíku

```
.
├── index.html                  # úvodní stránka
├── sluzby/index.html           # služby
├── showroom/index.html         # showroom (vč. kontaktního formuláře)
├── kontakty/index.html         # kontakty (vč. kontaktního formuláře)
├── ochrana-osobnich-udaju/index.html
├── send.php                    # zpracování formuláře, posílá na info@bftechnology.cz
├── www.automatizovany-dum.cz/  # všechny obrázky, CSS, JS, ikony
├── www.cltechsmarthome.cz/     # ikony externího CDN (lokálně)
├── cdn.jsdelivr.net/           # fancybox + cookie-bar (lokálně)
├── fonts.googleapis.com/       # CSS pro Nunito Sans
└── fonts.gstatic.com/          # font soubory (.ttf)
```

Všechny cesty jsou root-relativní (`/...`). Stránku tedy MUSÍ obsluhovat webserver - nelze otevřít přes `file://`.

## Co bylo upraveno oproti původnímu webu

1. **URL přepsány na lokální** - všechno se načítá z domény, na které web nasadíš (žádné odkazy na automatizovany-dum.cz, kromě explicitních textových).
2. **Kontaktní formulář** - posílá na `info@bftechnology.cz` přes `send.php` (PHP `mail()`). Honeypot ochrana proti spamu zachována.
3. **Banner po odeslání** - `kontakty/index.html` zobrazí zelený box po úspěchu, červený při chybě (parametry `?sent=1` / `?sent=0`).
4. **Instagram sekce** - nová sekce na homepage před patičkou s odkazem na profil + placeholder pro iframe widget (viz níže).
5. **Odstraněny WordPress-specific odkazy** - oEmbed, wp-json discovery (vracely by 404).
6. **GTM (Google Tag Manager)** - ponechán v HTML; pokud nechceš odesílat data, vymaž `<!-- Google Tag Manager -->` blok v každém HTML souboru.

## Požadavky na hosting

- **Statické soubory** (HTML/CSS/JS/obrázky) - jakýkoli hosting (Netlify, Cloudflare Pages, GitHub Pages, Forpsi, Wedos, Hostmaster, ...).
- **Kontaktní formulář** (`send.php`) - hosting s **PHP a funkcí `mail()`**, např. cokoli s LAMP/LEMP (běžné cz hostingy: Wedos, Forpsi, Active24, Webhouse).

### Alternativy bez PHP

Pokud chceš čistě statický hosting (Netlify / Cloudflare Pages), přepiš form action z `/send.php` na:
- **Formspree** - `https://formspree.io/f/TVOJ_ID` (free tier 50 zpráv / měsíc)
- **Web3Forms** - `https://api.web3forms.com/submit` (free, bez limitu)
- **Getform** - `https://getform.io/...`

Pak `send.php` můžeš smazat.

## Instagram widget - jak nasadit

V `index.html` u `<iframe src="https://cdn.lightwidget.com/widgets/REPLACE_WITH_YOUR_WIDGET_ID.html">`:

1. Jdi na https://lightwidget.com a zaregistruj se (zdarma).
2. "Create new widget" → propoj Instagram účet (`@bftechnology_sro` nebo jiný).
3. Vyber styl gridu (doporučuji 3×2 = 6 postů jako na bftechnology.cz).
4. Zkopíruj iframe src URL.
5. V `index.html` nahraď `REPLACE_WITH_YOUR_WIDGET_ID` skutečným ID.

Alternativy: SnapWidget, Behold.so, Elfsight (všechny mají free tier).

## Lokální test

Cokoli, co umí PHP-built-in server:

```bash
cd /Users/bj_air/Downloads/Claude/automatizovany-dum
php -S localhost:8000
```

Pak otevři http://localhost:8000.

Bez PHP (jen statika, formulář nebude fungovat):

```bash
ruby -run -e httpd . -p 8000
# nebo
python3 -m http.server 8000
```

## Nasazení - zkrácený postup

### Wedos / Active24 / Forpsi (PHP hosting)

1. FTP klientem (FileZilla / Cyberduck) nahraj celý obsah do `www/` nebo `public_html/`.
2. Nastav DNS doménu na hosting.
3. Otestuj formulář - vyplň, odešli, zkontroluj `info@bftechnology.cz`.

### Netlify / Cloudflare Pages (jen statika)

1. Nejdřív přepiš form action na Formspree/Web3Forms (viz výše) a smaž `send.php`.
2. Vytvoř Git repo, commitni soubory.
3. V Netlify / CF Pages → Import Git repo → Deploy.

## Známé limity

- **Bez WordPress backendu** = žádné CMS pro úpravy. Texty se mění přímo v HTML.
- **`fancybox` lightbox** může zobrazovat menu při kliku na obrázky - zachováno z originálu, funguje díky lokální kopii skriptu.
- **Cookie-bar** zachován (lišta GDPR). Cesta na "Ochrana osobních údajů" upravena na lokální stránku.
- **YouTube preconnect** zachovány v `<head>` - kdyby kdekoliv v obsahu byl embed.
