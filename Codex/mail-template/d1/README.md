# RailTime Mail-Template D1

Layout-3-inspiriertes E-Mail-Paket für RT Rail Time GmbH. Die versendbaren Vorlagen verwenden bewusst robuste Tabellen, Inline-CSS, PNG/JPG und E-Mail-sichere Systemschriften. SVG, Webfonts, Animationen, Flexbox und Grid werden in den eigentlichen Maildateien nicht benötigt.

## Inhalt

- `index.html` – lokale Übersicht und Vorschau aller Varianten
- `universal/email-body.html` – vollständige HTML-Body-Vorlage zum Öffnen/Kopieren
- `universal/RailTime-E-Mailvorlage.eml` – importierbare MIME-Mail mit Text- und HTML-Teil sowie eingebetteten CID-Bildern
- `universal/signature-dark.html` / `signature-light.html` – Signaturen mit relativen Assets
- `universal/signature-*-inline.html` – selbstständige HTML-Signaturen mit eingebettetem Logo, gut für Browser-Kopieren
- `universal/*.txt` – Nur-Text-Fallbacks
- `outlook/RailTime-E-Mailvorlage.oft` – klassische Outlook-Vorlage
- `outlook/RailTime-E-Mailvorlage.msg` – Outlook-Nachrichtenvorlage im Unicode-MSG-Format
- `outlook/RailTime-Signatur.htm/.rtf/.txt` plus `_files` – klassischer Outlook-Signatursatz
- `mazan/` – eigenständiger Body- und Signatursatz für Mazan mit den allgemeinen Firmendaten

## Vor der Verwendung

Alle Platzhalter in doppelten geschweiften Klammern ersetzen, insbesondere:

`{{ANREDE}}`, `{{NACHRICHT}}`, `{{VORNAME_NACHNAME}}`, `{{POSITION}}`, `{{DURCHWAHL}}`, `{{MOBIL}}`, `{{E_MAIL}}`, `{{GESCHAEFTSFUEHRUNG}}`, `{{REGISTERGERICHT}}`, `{{HRB}}`, `{{UST_ID}}`.

Geschäftsführung, Registergericht, Handelsregisternummer und USt-ID sind im Projekt nicht hinterlegt und wurden deshalb bewusst nicht erfunden. Diese Pflichtangaben müssen vor produktiver Nutzung ergänzt werden. Optionale Einsatzdatenzeilen können entfernt werden, wenn sie für eine Nachricht nicht benötigt werden.

## Outlook (klassisch)

1. Für den Body `outlook/RailTime-E-Mailvorlage.oft` doppelklicken und anschließend die Platzhalter bearbeiten.
2. Für die Signatur PowerShell im Ordner `outlook` öffnen und `./install-signature.ps1` ausführen. Mit `-WhatIf` lässt sich der Kopiervorgang vorab anzeigen.
3. In Outlook unter **Datei → Optionen → E-Mail → Signaturen** die Signatur `RailTime-Signatur` einem Konto zuordnen.

Body und HTML-Signaturen sind auf `100%` Breite ausgelegt und füllen die verfügbare Nachrichtenbreite. Nach einer Aktualisierung des Pakets das Installationsskript erneut ausführen und bereits geöffnete Vorlagen schließen und neu öffnen, damit Outlook nicht die ältere lokale Version weiterverwendet.

Das Installationsskript setzt keine Standardsignatur und ändert keine Outlook-Registrywerte. Neues Outlook unterstützt den klassischen OFT-/COM-Workflow nicht vollständig; dort vorzugsweise EML oder die HTML-Kopiervariante verwenden.

## Gmail, Apple Mail, Thunderbird und Webmailer

- `universal/signature-dark-inline.html` oder `signature-light-inline.html` im Browser öffnen, den sichtbaren Signaturblock markieren und in den Signatur-Editor kopieren.
- Für eine vollständige Nachricht `universal/email-body.html` öffnen und den gerenderten Inhalt übernehmen.
- Thunderbird und viele Desktop-Clients können `RailTime-E-Mailvorlage.eml` direkt öffnen oder importieren.
- Nach dem Einfügen immer eine Testmail an Outlook Desktop, Outlook Web, Gmail und ein Mobilgerät senden. Einige Clients verändern Dark-Mode-Farben automatisch.

## Neu erzeugen und prüfen

```powershell
python build-formats.py
powershell -ExecutionPolicy Bypass -File build-outlook.ps1
python verify-formats.py
```

`build-outlook.ps1` erstellt lokale OFT-/MSG-Dateien über das installierte klassische Outlook. Das Skript versendet nichts und trägt keinen Empfänger ein.
