# RT Logo-Icon Redesign — Entwurf d1

Umsetzung nach `Logo-Icon-Redesign-mockup.png` (Grundverzeichnis): rotes **R** (glänzend),
verschränkt mit dunkelgrauem **T** (matt-metallisch, leicht zurückversetzt). CI-Farben der
Liveseite rail-time.de: Signalrot `#e4002b` · Anthrazit `#090c11`.

## Dateien

| Datei | Beschreibung |
|---|---|
| `rt-logo.svg` | Vektor-Master (viewBox 1000×1000, transparenter Hintergrund). Alle weiteren Formate sind daraus abgeleitet. |
| `rt-logo.png` | 2000 × 2000 px, RGBA mit transparentem Hintergrund. |
| `rt-logo.glb` | 3D-Modell, glTF 2.0 binär. Maße 0,86 × 1,00 × 0,18 Einheiten (schmale Tiefe), zentriert am Ursprung. PBR Metallic/Roughness: `rot-glaenzend` (metalness 0.25 / roughness 0.22), `dunkelgrau-matt-metallisch` (metalness 0.85 / roughness 0.5). Fasen an allen Kanten. |
| `index.html` | Three.js-Viewer (GLTFLoader, unpkg three@0.161.0). Langsame Präsentationsdrehung, per Maus/Touch drehbar. |

## Animation per JavaScript

Der Viewer stellt eine kleine API bereit:

```js
rtLogo.setSpin(0.02);      // Drehgeschwindigkeit ändern (0 = stoppen)
rtLogo.setPose(0, 0);      // Frontansicht
rtLogo.setPose(-0.55, 0.08); // 3/4-Ansicht rechts
rtLogo.model;              // THREE.Group "rt-logo" für eigene Animationen
```

Im GLB heißen die Meshes `r-rot-1 … r-rot-4` (R-Teile) und `t-dunkel-1 … t-dunkel-2`
(T-Teile) — sie lassen sich damit auch einzeln animieren (z. B. T herausfahren).

Aufruf lokal: `http://localhost/RailTime/Claude/logo/d1/`
