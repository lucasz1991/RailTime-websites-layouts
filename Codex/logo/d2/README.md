# RT Logo D2 — dezenteres T, größerer Abstand

Weiterentwicklung von `Codex/logo/d1`: Das anthrazitfarbene T ist schlanker,
alle Innenkanten rücken gleichmäßig vom roten R ab. Die Außensilhouette
(linke Kante x=0, Grundlinie y=1000) bleibt unverändert bündig.

## Geometrie (viewBox 1000×1000)

| Maß | D1 | D2 |
| --- | --- | --- |
| Abstand R/T (Kanal) | 24 | **40** |
| T-Querbalken (Stärke) | 202 | 170 |
| T-Stamm (Breite) | 207 | 175 |
| Fasenbreite | 9 | 9 |

Konstruktionsregel: Jede dem R zugewandte T-Kante wurde um 16 Einheiten
zurückgenommen (24 → 40). Farben, Verläufe und Fasen sind identisch zu D1.

## Dateien

- `rt-logo.svg` — D2, Abstand 40 (Geometriequelle und Empfehlung)
- `rt-logo.glb` — eigenständiges 3D-Modell im GLB-2.0-Format
- `build-logo-glb.mjs` — dependency-freier Generator für das GLB
- `preview.html` — interaktive 3D-Vorschau mit vier Kameraansichten
- `rt-logo-variante-32.svg` — sanftere Variante, Abstand 32
- `rt-logo-variante-48.svg` — stärkere Variante, Abstand 48
- `vergleich-d1-d2.png` — Vergleichsmockup D1 / 32 / 40 / 48

## 3D-Aufbau

- Abmessungen: 1 × 1 × 0,18 Einheiten
- Konstruktionsabstand R/T: 0,04 Einheiten
- Getrennte Knoten `R_Red` und `T_Dark` unter `RT_Logo`
- Physische Fasen an Vorder- und Rückseite aller Außen- und Innenkanten
- Separate PBR-Materialien für Fläche, Fase und Seitenwand
- Eingebettete Geometrie ohne externe BIN-Datei oder Texturen

Die feinen hellen Kanten in `preview.html` dienen nur der besseren Sichtprüfung.
Die umlaufenden Fasen selbst sind echte Geometrie im GLB.

## Erzeugen und ansehen

```powershell
cd C:\xampp\htdocs\RailTime\Codex\logo\d2
node build-logo-glb.mjs
```

Vorschau über den lokalen Webserver öffnen:

```text
http://localhost/RailTime/Codex/logo/d2/preview.html
```

## Aktivierung

Die Live-Bildmarke wird in `Shared/components/site-shell.php` →
`rt_logo_lockup()` referenziert. Für die Umstellung den Pfad dort auf
`Codex/logo/d2/rt-logo.svg` ändern. Diese Aktivierung ist bewusst nicht
Bestandteil des D2-Asset-Pakets.

