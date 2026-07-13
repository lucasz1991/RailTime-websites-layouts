# RailTime Design Workspace

Die einzige aktive Projektquelle ist `C:\xampp\htdocs\RailTime` und wird über `http://localhost/RailTime/` geprüft.

## Codex-Layouts

- Layout 1: Noir Motion
- Layout 2: Signal Compact
- Layout 3: Signal Atlas
- Layout 4: Atlas Editorial
- Layout 5: Horizon Signature

Gemeinsame Texte, Bilder, Videos, Logos, Kartenmodule, Tailwind und ScrollMagic liegen unter `Shared`. Layoutspezifische Gestaltung und Bewegung liegen im jeweiligen Ordner unter `Codex`.

Die verbindlichen Vorgaben stehen in `Layout Rules.csv`; jedes Layout beschreibt sein individuelles Konzept zusätzlich in einer eigenen `layout.csv`.

Die öffentliche Vorschau läuft ohne sichtbare AI-Ordnerstruktur über nummerierte Pfade unter `http://localhost/RailTime/layouts/1` bis `http://localhost/RailTime/layouts/7`. Unterseiten folgen demselben Schema, zum Beispiel `http://localhost/RailTime/layouts/3/leistungen`.

Das Startvideo nutzt layoutübergreifend das echte erste Videoframe `Shared/assets/images/start3-first-frame.png` als Ladeposter. Signal Atlas startet es direkt und stumm, sperrt das Seiten-Scrollen bis zum tatsächlichen Videoende und blendet erst danach D2-3D-Logo, Navigation und Hero-Overlays ein.

Noir Motion und Horizon Signature nutzen zentral `Shared/scripts/scroll-video-engine.js`. Die ersten 30 Prozent des Startvideos werden übersprungen; der verbleibende Clip wird für Scroll-, Logo- und Hero-Übergänge wieder sauber auf 0 bis 100 Prozent normiert. Signal Atlas ist davon bewusst entkoppelt; nach seinem einmaligen Startvideo bewegt sich ausschließlich die große D2-3D-Bildmarke im Hero dauerhaft über einen weichen 260-Grad-Bogen.

Die Deutschlandkarte steht auf den Startseiten im unteren Standort-/Techniksegment links neben dem Text. In diesem Segment wird kein zusätzliches Foto verwendet.

Die Layoutübersicht zeigt auf Desktop zwei gleich hohe Karten nebeneinander. Screenshots füllen die Kartenhöhe vollständig und behalten eine automatische Breite.
