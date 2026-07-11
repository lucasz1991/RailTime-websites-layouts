# RailTime Logo D1

Die D1-Variante setzt das Mockup als verschränktes RT-Monogramm um: ein glänzend signalrotes R (`#e4002b`) umschließt ein matt-metallisches, anthrazitfarbenes T.

## Dateien

- `rt-logo.svg` – orthogonale 2D-Frontprojektion des 3D-Modells mit transparenter Fläche und einer nativen Größe von 2000 × 2000 Pixeln
- `rt-logo.glb` – binäres glTF-2.0-Modell mit eingebetteter Geometrie und PBR-Materialien
- `preview.html` – kleine Three.js-Vorschau mit Dreh- und Zoomsteuerung
- `build-logo-glb.mjs` – dependency-freier Generator für das GLB-Modell

## 3D-Spezifikation

- Außenmaß exakt `1 × 1 × 0,18`
- Ursprung im geometrischen Mittelpunkt
- Vorderseite auf `+Z`, Hochachse `+Y`
- getrennte Knoten `R_Red` und `T_Dark` unter `RT_Logo`
- echte Extrusion mit feiner Fase und einem kompakten R/T-Abstand von 0,024 Einheiten
- sechs eingebettete PBR-Materialien für Vorderflächen, Fasen und Seiten
- keine Texturen, externen Binärdateien oder optionalen glTF-Erweiterungen

Neu erzeugen:

```powershell
node build-logo-glb.mjs
```

Lokale Vorschau:

```text
http://localhost/RailTime/Codex/logo/d1/preview.html
```

Das gesamte Logo lässt sich über `RT_Logo` animieren. Für getrennte Bewegungen können `R_Red` und `T_Dark` direkt angesprochen werden:

```js
const logo = gltf.scene.getObjectByName('RT_Logo');
const redR = gltf.scene.getObjectByName('R_Red');
const darkT = gltf.scene.getObjectByName('T_Dark');

logo.rotation.y += 0.01;
redR.position.z = Math.sin(performance.now() * 0.002) * 0.02;
darkT.rotation.x = 0.08;
```
