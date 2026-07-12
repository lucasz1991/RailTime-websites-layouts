# RT Logo-Icon — Claude D1 (schmale Fuge)

RT-Monogramm nach `Logo-Icon-Redesign-mockup.png`: glänzend signalrotes **R**,
matt-metallisches anthrazitfarbenes **T**. Geometrie-Methode nach Codex/logo/d1,
jedoch mit **schmalerer Fuge: 16 statt 30 Einheiten** Freiraum rund um das T.
CI-Farben: Signalrot `#e4002b` · Anthrazit `#090c11`.

## Dateien

| Datei | Beschreibung |
|---|---|
| `rt-logo.svg` | Vektor-Master (0..1000-Geometrie, transparent, dezente 2.5D-Tiefe, keine Kontur-Strokes). |
| `rt-logo.png` | 2000 × 2000 px, RGBA transparent, aus dem SVG gerendert. |
| `rt-logo.glb` | glTF 2.0 binär, 1 × 1 × 0,18 Einheiten, zentriert. 6 PBR-Materialien (Face/Bevel/Side je Buchstabe), Nodes `RT_Logo` → `R_Red` + `T_Dark` — kompatibel mit `Shared/scripts/logo-3d.js`. |
| `build-logo-glb.mjs` | Abhängigkeitsfreier Builder: `node build-logo-glb.mjs` (Fuge über `GAP` dokumentiert, Polygone oben in der Datei). |
| `index.html` | Three.js-Viewer (jsdelivr, OrbitControls, ACES-Tonemapping): sanftes Pendeln, Ziehen zum Drehen, Mausrad-Zoom. API: `rtLogo.setSway(v)`, `rtLogo.setPose(y, x)`, `rtLogo.model`. |

Aufruf lokal: `http://localhost/RailTime/Claude/logo/d1/`

Hinweis: Damit Ordner-URLs mit `index.html` funktionieren, steht in der
Projekt-`.htaccess` `DirectoryIndex index.php index.html`.
