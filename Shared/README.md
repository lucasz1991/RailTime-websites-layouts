# RailTime Shared Design System

Dieser Ordner ist die einzige gemeinsame Quelle für Codex- und Claude-Layouts.

## Gemeinsame Bausteine

- `assets/images`: Logos und RailTime-Bilder
- `assets/video`: aufbereitete und originale Startvideo-Datei
- `assets/brands`: zentral verfügbare Platzhalter-Kundenlogos
- `content/railtime-content.php`: Texte, Navigation, Kennzahlen, Leistungen, Prozess und Medienzuordnung
- `components/site-shell.php`: Dokumentkopf, Menü, Footer und Skripteinbindung
- `components/segments.php`: wiederverwendbare Startseiten-Segmente
- `components/render-home.php`: gemeinsame Startseiten-Komposition
- `components/subpages.php`: Leistungen, Kontakt, Über uns, Impressum und Datenschutz
- `modules/germany-map.php`: gemeinsame Deutschlandkarte
- `styles`: gemeinsames Design-System
- `scripts`: gemeinsame Intro-, Akkordeon- und Unterseitenlogik
- `vendor`: lokale Drittanbieterdateien für Tailwind und ScrollMagic

## Minimaler Layoutordner

Ein Layout benötigt nur die sechs PHP-Seitendateien (plus optional eine `partials.php`), `assets/layout.css`, `assets/motion.js` und eine beschreibende `layout.csv`. Screenshot-Ordner und statische HTML-Exporte entfallen — die Layoutübersicht rendert alle Vorschauen live als iframes über die öffentlichen Pfade `/RailTime/layouts/N`.

```php
<?php require __DIR__.'/../../Shared/components/render-home.php'; rt_home(1); ?>
<?php require __DIR__.'/../../Shared/components/subpages.php'; rt_about(1); ?>
<?php require __DIR__.'/../../Shared/components/subpages.php'; rt_services(1); ?>
<?php require __DIR__.'/../../Shared/components/subpages.php'; rt_contact(1); ?>
<?php require __DIR__.'/../../Shared/components/subpages.php'; rt_imprint(1); ?>
<?php require __DIR__.'/../../Shared/components/subpages.php'; rt_privacy(1); ?>
```

Die Zahl entspricht dem Layoutprofil. Gemeinsam benötigte Inhalte oder Segmente niemals in einen Codex- oder Claude-Layoutordner kopieren, sondern hier ergänzen. Layoutordner enthalten ausschließlich individuelle Gestaltung und Bewegung.

## Farb- und Corporate Identity (verbindlich für alle Layouts)

Die Farbidentität stammt aus der Liveseite **rail-time.de** und ist in `styles/design-system.css` hinterlegt. Sie ist für **jedes** Layout – Codex wie Claude – unveränderlich:

- **Signalrot** `#e4002b` – einziger Farbakzent
- **Anthrazit / Schwarz** `#090c11` – dunkler Grundton
- **Weiß / Hell** `#f4f2ed` – helle Segmente und Text auf Dunkel

Keine erfundenen Zusatzfarben, keine abweichenden Rottöne, keine Zweitakzente. Rot bleibt der einzige Akzent.

### Grundtonalität

Jedes Layout ist **überwiegend dunkel** (Anthrazit, Schwarz oder ein anderer dunkler Grundton). Der dunkle Ton dominiert; **helle bzw. weiße Segmente sind erwünscht**, um Rhythmus und Kontrast zu schaffen, bleiben aber Akzent und bestimmen nicht die Gesamtwirkung. Wie viel Hell/Dunkel und in welcher Anordnung, variiert bewusst je Layout.

### Was die Layout-Regeln festlegen – und was nicht

Die `Layout Rules.csv` und die layoutspezifischen Konzepte beschreiben **ausschließlich konzeptionelle Layout-Variablen**:

- Struktur und Raster (Grid, Komposition, Seitenaufbau)
- Scroll- und Bewegungseffekte (an realen Scrollfortschritt gebunden)
- Typografie-Rhythmus und Textbehandlung (Schriftwahl, Größen, Hierarchie, Kompaktheit)
- Interaktion (Akkordeon, Slider, Hover, Video-Intro)

Die **Farbwelt ist keine Gestaltungs- oder Unterscheidungsachse** – sie ist durch die feste CI vorgegeben. Layouts unterscheiden sich also strukturell, typografisch und in der Bewegung, nicht farblich. Template-Referenzen (Webflow/ThemeForest) dienen nur als Vorbild für Struktur, Komposition und Anmutung, niemals für die Farbgebung.
