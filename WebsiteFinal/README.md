# RT Rail Time – WebsiteFinal

Dieses Verzeichnis ist das eigenständige Veröffentlichungspaket von Layout 3. Es enthält alle benötigten PHP-, CSS-, JavaScript-, Font-, Bild-, Video-, Logo- und 3D-Dateien. Die Website lädt im Betrieb keine Google Fonts, kein externes Three.js und keine Dateien aus `Shared` oder `Codex`.

## Vor der Veröffentlichung zwingend ergänzen

In `config/site.php` fehlen absichtlich Angaben, die aus dem vorhandenen Projekt nicht rechtsverbindlich ermittelt werden konnten:

- vertretungsberechtigte Geschäftsführung
- Registergericht und Registernummer
- Umsatzsteuer-ID
- Berufshaftpflichtversicherung
- Hosting-Anbieter und Speicherdauer der Server-Protokolle

Solange diese Werte fehlen, zeigen Impressum und Datenschutz sichtbare gelbe Hinweise und sind per `noindex` von der Suchmaschinen-Indexierung ausgeschlossen. Nach vollständiger Ergänzung werden sie automatisch indexierbar und in die Sitemap aufgenommen. Die Rechtstexte sollten vor dem Livegang fachlich geprüft werden.

## Veröffentlichung auf Apache / Plesk

1. Den **Inhalt** von `WebsiteFinal/` in das gewünschte Webroot hochladen, zum Beispiel `httpdocs/`.
2. PHP 8.1 oder neuer mit `mbstring` aktivieren.
3. Apache `mod_rewrite` aktivieren und das Überschreiben per `.htaccess` erlauben (`AllowOverride FileInfo` bzw. `All`).
4. Folgende Server-Umgebungsvariablen setzen:
   - `RAILTIME_SITE_URL=https://www.rail-time.de`
   - `RAILTIME_MAIL_ENABLED=1`
   - `RAILTIME_NOINDEX=0`
5. Den PHP-Mailversand des Servers für `kontakt@rail-time.de` einrichten und das Kontaktformular testen.
6. `https://www.rail-time.de/sitemap.xml` in Google Search Console und Bing Webmaster Tools einreichen.

Die `.htaccess` erzwingt außerhalb von localhost HTTPS, liefert echte 404-Seiten aus, setzt Sicherheitsheader und leitet alte `.html`-/`.php`-Adressen auf die sauberen URLs um.

## Lokaler Test

Unter XAMPP ist das Paket nach Ablage im RailTime-Projekt unter folgender Adresse erreichbar:

```text
http://localhost/RailTime/WebsiteFinal/
```

Alternativ mit dem eingebauten PHP-Server:

```powershell
cd C:\xampp\htdocs\RailTime\WebsiteFinal
php -S 127.0.0.1:8080 router.php
```

## Saubere Routen

- `/` – Startseite
- `/leistungen`
- `/ueber-uns`
- `/kontakt`
- `/impressum`
- `/datenschutz`
- `/robots.txt`
- `/sitemap.xml`

## Nginx-Hinweis

Nginx wertet `.htaccess` nicht aus. Im Serverblock mindestens Folgendes ergänzen:

```nginx
location ~ ^/(config|src)(/|$) { deny all; }
location / { try_files $uri $uri/ /index.php?$query_string; }

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
}
```

Den PHP-FPM-Socket an die tatsächlich installierte PHP-Version anpassen. HTTPS und Sicherheitsheader sollten bei Nginx zusätzlich auf Serverebene gesetzt werden.

## SEO-Bestandteile

Jede öffentliche Inhaltsseite besitzt einen eigenen Titel und eine eigene Beschreibung, Canonical- und Hreflang-Angaben, Open-Graph-/Twitter-Metadaten sowie strukturierte Daten. Die Leistungsseite enthält zusätzlich Service- und Breadcrumb-Daten. Sitemap und robots.txt werden dynamisch aus der konfigurierten Hauptdomain erzeugt.

Für einen Staging-Server `RAILTIME_NOINDEX=1` setzen. Dann werden alle Seiten mit `noindex,nofollow` ausgeliefert und robots.txt sperrt das Crawling.
