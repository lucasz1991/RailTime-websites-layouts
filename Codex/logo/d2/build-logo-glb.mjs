import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const OUTPUT = join(dirname(fileURLToPath(import.meta.url)), 'rt-logo.glb');
const HALF_DEPTH = 0.09;
const BEVEL_DEPTH = 0.015;
const BEVEL_SIZE = 0.009;

// The same 0..1000 D2 frontal geometry used by rt-logo.svg.
// Every polygon perimeter receives a physical front bevel, side wall and back bevel.
const redPolygons = [
  [
    [0, 0], [738, 0], [862, 152], [862, 462], [755, 583], [617, 583],
    [1000, 1000], [731, 1000], [460, 658], [460, 410], [675, 410],
    [715, 364], [715, 206], [673, 160], [0, 160],
  ],
  [[0, 410], [205, 410], [205, 1000], [0, 1000]],
];

const darkPolygons = [[
  [0, 200], [670, 200], [670, 370], [420, 370],
  [420, 1000], [245, 1000], [245, 370], [0, 370],
]];

const srgb = (hex) => {
  const channels = hex.match(/[a-f\d]{2}/gi).map((value) => parseInt(value, 16) / 255);
  return channels.map((value) => value <= 0.04045
    ? value / 12.92
    : ((value + 0.055) / 1.055) ** 2.4);
};

const material = (name, hex, metallicFactor, roughnessFactor) => ({
  name,
  pbrMetallicRoughness: {
    baseColorFactor: [...srgb(hex), 1],
    metallicFactor,
    roughnessFactor,
  },
});

const materials = [
  material('RT Red Gloss', 'e4002b', 0.18, 0.2),
  material('RT Red Bevel', 'ff2949', 0.22, 0.16),
  material('RT Red Side', '790017', 0.25, 0.34),
  material('RT Anthracite Metallic', '30353b', 0.78, 0.5),
  material('RT Anthracite Bevel', '69727b', 0.82, 0.34),
  material('RT Anthracite Side', '11151a', 0.82, 0.62),
];

function signedArea(points) {
  let area = 0;
  for (let i = 0; i < points.length; i += 1) {
    const [x1, y1] = points[i];
    const [x2, y2] = points[(i + 1) % points.length];
    area += x1 * y2 - x2 * y1;
  }
  return area / 2;
}

function normalizePolygon(points) {
  const normalized = points.map(([x, y]) => [x / 1000 - 0.5, 0.5 - y / 1000]);
  return signedArea(normalized) > 0 ? normalized : normalized.reverse();
}

function cross2(a, b, c) {
  return (b[0] - a[0]) * (c[1] - a[1]) - (b[1] - a[1]) * (c[0] - a[0]);
}

function pointInTriangle(point, a, b, c) {
  const ab = cross2(a, b, point);
  const bc = cross2(b, c, point);
  const ca = cross2(c, a, point);
  const epsilon = 1e-10;
  return ab >= -epsilon && bc >= -epsilon && ca >= -epsilon;
}

function triangulate(points) {
  const remaining = points.map((_, index) => index);
  const triangles = [];
  let guard = points.length * points.length;

  while (remaining.length > 3 && guard > 0) {
    let clipped = false;
    for (let i = 0; i < remaining.length; i += 1) {
      const previous = remaining[(i - 1 + remaining.length) % remaining.length];
      const current = remaining[i];
      const next = remaining[(i + 1) % remaining.length];
      const a = points[previous];
      const b = points[current];
      const c = points[next];

      if (cross2(a, b, c) <= 1e-10) continue;

      const containsVertex = remaining.some((candidate) => candidate !== previous
        && candidate !== current
        && candidate !== next
        && pointInTriangle(points[candidate], a, b, c));

      if (containsVertex) continue;
      triangles.push([previous, current, next]);
      remaining.splice(i, 1);
      clipped = true;
      break;
    }

    if (!clipped) {
      throw new Error('Polygon triangulation failed; check for a self-intersection.');
    }
    guard -= 1;
  }

  if (remaining.length === 3) triangles.push([...remaining]);
  return triangles;
}

function lineIntersection(a, directionA, b, directionB) {
  const denominator = directionA[0] * directionB[1] - directionA[1] * directionB[0];
  if (Math.abs(denominator) < 1e-10) return null;
  const delta = [b[0] - a[0], b[1] - a[1]];
  const t = (delta[0] * directionB[1] - delta[1] * directionB[0]) / denominator;
  return [a[0] + directionA[0] * t, a[1] + directionA[1] * t];
}

function insetPolygon(points, distance) {
  return points.map((point, index) => {
    const previous = points[(index - 1 + points.length) % points.length];
    const next = points[(index + 1) % points.length];
    const edgeIn = [point[0] - previous[0], point[1] - previous[1]];
    const edgeOut = [next[0] - point[0], next[1] - point[1]];
    const lengthIn = Math.hypot(...edgeIn);
    const lengthOut = Math.hypot(...edgeOut);
    const directionIn = [edgeIn[0] / lengthIn, edgeIn[1] / lengthIn];
    const directionOut = [edgeOut[0] / lengthOut, edgeOut[1] / lengthOut];
    const normalIn = [-directionIn[1], directionIn[0]];
    const normalOut = [-directionOut[1], directionOut[0]];
    const lineIn = [point[0] + normalIn[0] * distance, point[1] + normalIn[1] * distance];
    const lineOut = [point[0] + normalOut[0] * distance, point[1] + normalOut[1] * distance];
    const intersection = lineIntersection(lineIn, directionIn, lineOut, directionOut);

    if (!intersection || Math.hypot(intersection[0] - point[0], intersection[1] - point[1]) > distance * 4) {
      const average = [normalIn[0] + normalOut[0], normalIn[1] + normalOut[1]];
      const length = Math.hypot(...average) || 1;
      return [point[0] + average[0] / length * distance, point[1] + average[1] / length * distance];
    }
    return intersection;
  });
}

function emptyGeometry() {
  return { positions: [], normals: [] };
}

function addTriangle(geometry, a, b, c) {
  const ab = [b[0] - a[0], b[1] - a[1], b[2] - a[2]];
  const ac = [c[0] - a[0], c[1] - a[1], c[2] - a[2]];
  const normal = [
    ab[1] * ac[2] - ab[2] * ac[1],
    ab[2] * ac[0] - ab[0] * ac[2],
    ab[0] * ac[1] - ab[1] * ac[0],
  ];
  const length = Math.hypot(...normal) || 1;
  const unit = normal.map((value) => value / length);
  geometry.positions.push(...a, ...b, ...c);
  geometry.normals.push(...unit, ...unit, ...unit);
}

function addQuad(geometry, a, b, c, d) {
  addTriangle(geometry, a, b, c);
  addTriangle(geometry, a, c, d);
}

function extrudePolygon(rawPoints, target) {
  const outer = normalizePolygon(rawPoints);
  const inner = insetPolygon(outer, BEVEL_SIZE);
  const frontTriangles = triangulate(inner);
  const outerFrontZ = HALF_DEPTH - BEVEL_DEPTH;
  const outerBackZ = -HALF_DEPTH + BEVEL_DEPTH;

  for (const [a, b, c] of frontTriangles) {
    addTriangle(target.face, [...inner[a], HALF_DEPTH], [...inner[b], HALF_DEPTH], [...inner[c], HALF_DEPTH]);
    addTriangle(target.side, [...inner[c], -HALF_DEPTH], [...inner[b], -HALF_DEPTH], [...inner[a], -HALF_DEPTH]);
  }

  for (let index = 0; index < outer.length; index += 1) {
    const next = (index + 1) % outer.length;
    addQuad(
      target.bevel,
      [...outer[index], outerFrontZ],
      [...outer[next], outerFrontZ],
      [...inner[next], HALF_DEPTH],
      [...inner[index], HALF_DEPTH],
    );
    addQuad(
      target.side,
      [...outer[index], outerBackZ],
      [...outer[next], outerBackZ],
      [...outer[next], outerFrontZ],
      [...outer[index], outerFrontZ],
    );
    addQuad(
      target.bevel,
      [...outer[index], outerBackZ],
      [...inner[index], -HALF_DEPTH],
      [...inner[next], -HALF_DEPTH],
      [...outer[next], outerBackZ],
    );
  }
}

function buildPart(polygons) {
  const part = { face: emptyGeometry(), bevel: emptyGeometry(), side: emptyGeometry() };
  polygons.forEach((polygon) => extrudePolygon(polygon, part));
  return part;
}

const red = buildPart(redPolygons);
const dark = buildPart(darkPolygons);
const chunks = [];
const bufferViews = [];
const accessors = [];

function align4(value) {
  return (value + 3) & ~3;
}

function appendFloatAccessor(values, type, includeBounds = false) {
  const floatValues = Float32Array.from(values);
  const byteOffset = chunks.reduce((total, chunk) => total + chunk.length, 0);
  const padding = align4(byteOffset) - byteOffset;
  if (padding) chunks.push(Buffer.alloc(padding));
  const alignedOffset = byteOffset + padding;
  const buffer = Buffer.from(floatValues.buffer, floatValues.byteOffset, floatValues.byteLength);
  chunks.push(buffer);

  const bufferView = bufferViews.push({
    buffer: 0,
    byteOffset: alignedOffset,
    byteLength: buffer.byteLength,
    target: 34962,
  }) - 1;

  const componentCount = type === 'VEC3' ? 3 : 1;
  const accessor = {
    bufferView,
    componentType: 5126,
    count: values.length / componentCount,
    type,
  };

  if (includeBounds) {
    accessor.min = [Infinity, Infinity, Infinity];
    accessor.max = [-Infinity, -Infinity, -Infinity];
    for (let index = 0; index < values.length; index += 3) {
      for (let axis = 0; axis < 3; axis += 1) {
        accessor.min[axis] = Math.min(accessor.min[axis], values[index + axis]);
        accessor.max[axis] = Math.max(accessor.max[axis], values[index + axis]);
      }
    }
  }

  return accessors.push(accessor) - 1;
}

function primitive(geometry, materialIndex) {
  return {
    attributes: {
      POSITION: appendFloatAccessor(geometry.positions, 'VEC3', true),
      NORMAL: appendFloatAccessor(geometry.normals, 'VEC3'),
    },
    material: materialIndex,
    mode: 4,
  };
}

const meshes = [
  {
    name: 'R_Red_Mesh',
    primitives: [primitive(red.face, 0), primitive(red.bevel, 1), primitive(red.side, 2)],
  },
  {
    name: 'T_Dark_Mesh',
    primitives: [primitive(dark.face, 3), primitive(dark.bevel, 4), primitive(dark.side, 5)],
  },
];

const binary = Buffer.concat(chunks);
const gltf = {
  asset: {
    version: '2.0',
    generator: 'RailTime D2 dependency-free GLB builder',
    copyright: 'RailTime GmbH',
  },
  scene: 0,
  scenes: [{ name: 'RT Logo Scene', nodes: [0] }],
  nodes: [
    {
      name: 'RT_Logo',
      children: [1, 2],
      extras: {
        dimensions: [1, 1, 0.18],
        designGap: 0.04,
        gapUnits: 40,
        bevelSize: BEVEL_SIZE,
        bevelDepth: BEVEL_DEPTH,
        beveledPerimeterEdges: 'front-and-back',
        frontAxis: '+Z',
        upAxis: '+Y',
      },
    },
    { name: 'R_Red', mesh: 0 },
    { name: 'T_Dark', mesh: 1 },
  ],
  meshes,
  materials,
  buffers: [{ byteLength: binary.byteLength }],
  bufferViews,
  accessors,
};

function paddedJson(value) {
  const source = Buffer.from(JSON.stringify(value), 'utf8');
  const padded = Buffer.alloc(align4(source.length), 0x20);
  source.copy(padded);
  return padded;
}

const jsonChunk = paddedJson(gltf);
const binPadding = Buffer.alloc(align4(binary.length) - binary.length);
const binChunk = Buffer.concat([binary, binPadding]);
const totalLength = 12 + 8 + jsonChunk.length + 8 + binChunk.length;
const header = Buffer.alloc(12);
header.writeUInt32LE(0x46546c67, 0);
header.writeUInt32LE(2, 4);
header.writeUInt32LE(totalLength, 8);
const jsonHeader = Buffer.alloc(8);
jsonHeader.writeUInt32LE(jsonChunk.length, 0);
jsonHeader.writeUInt32LE(0x4e4f534a, 4);
const binHeader = Buffer.alloc(8);
binHeader.writeUInt32LE(binChunk.length, 0);
binHeader.writeUInt32LE(0x004e4942, 4);

writeFileSync(OUTPUT, Buffer.concat([header, jsonHeader, jsonChunk, binHeader, binChunk]));
console.log(`Wrote ${OUTPUT}`);
console.log(`Bounds: 1 × 1 × ${(HALF_DEPTH * 2).toFixed(2)}; materials: ${materials.length}; meshes: ${meshes.length}`);
