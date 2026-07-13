from __future__ import annotations

import subprocess
import tempfile
from collections import Counter
from pathlib import Path

from PIL import Image


ROOT = Path(__file__).resolve().parents[2]
SOURCE_SVG = ROOT / "Codex" / "logo" / "d2" / "rt-logo.svg"
IMAGES = ROOT / "Shared" / "assets" / "images"
ICONS = ROOT / "Shared" / "assets" / "icons"


def chrome_path() -> Path:
    candidates = [
        Path(r"C:\Program Files\Google\Chrome\Application\chrome.exe"),
        Path(r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"),
        Path(r"C:\Program Files\Microsoft\Edge\Application\msedge.exe"),
    ]
    for candidate in candidates:
        if candidate.is_file():
            return candidate
    raise FileNotFoundError("Chrome oder Edge wurde nicht gefunden.")


def render_master() -> Image.Image:
    with tempfile.TemporaryDirectory(prefix="railtime-logo-") as folder:
        folder_path = Path(folder)
        output = folder_path / "rt-logo-d2.png"
        profile = folder_path / "browser-profile"
        command = [
            str(chrome_path()),
            "--headless=new",
            "--disable-gpu",
            "--hide-scrollbars",
            "--no-first-run",
            "--disable-extensions",
            "--force-device-scale-factor=1",
            "--default-background-color=00000000",
            "--window-size=2000,2000",
            f"--user-data-dir={profile}",
            f"--screenshot={output}",
            SOURCE_SVG.as_uri(),
        ]
        subprocess.run(command, check=True, timeout=45)
        with Image.open(output) as rendered:
            return rendered.convert("RGBA")


def resize(image: Image.Image, size: tuple[int, int]) -> Image.Image:
    return image.resize(size, Image.Resampling.LANCZOS)


def padded_icon(image: Image.Image, size: int, ratio: float = .86) -> Image.Image:
    inner = max(1, round(size * ratio))
    target = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    mark = resize(image, (inner, inner))
    offset = (size - inner) // 2
    target.alpha_composite(mark, (offset, offset))
    return target


def replace_region(path: Path, box: tuple[int, int, int, int], mark: Image.Image, position: tuple[int, int]) -> None:
    with Image.open(path) as current:
        target = current.convert("RGBA")
    transparent = Image.new("RGBA", (box[2] - box[0], box[3] - box[1]), (0, 0, 0, 0))
    target.paste(transparent, box)
    target.alpha_composite(mark, position)
    target.save(path, optimize=True)


def time_silver_palette(wordmark: Image.Image) -> list[tuple[int, int, int]]:
    """Read the dominant vertical fill colours directly from the TIME lettering."""
    colours: list[tuple[int, int, int]] = []
    for y in range(80, 251):
        row = [
            wordmark.getpixel((x, y))[:3]
            for x in range(946, min(1730, wordmark.width))
            if wordmark.getpixel((x, y))[3] >= 240
        ]
        if row:
            colours.append(Counter(row).most_common(1)[0][0])
        elif colours:
            colours.append(colours[-1])
    if not colours:
        raise RuntimeError("Der TIME-Silberverlauf konnte nicht aus logo-txt.png gelesen werden.")
    return colours


def silver_region(
    image: Image.Image,
    box: tuple[int, int, int, int],
    palette: list[tuple[int, int, int]],
) -> None:
    left, top, right, bottom = box
    span = max(1, bottom - top - 1)
    for y in range(top, min(bottom, image.height)):
        palette_y = round((y - top) / span * (len(palette) - 1))
        red, green, blue = palette[palette_y]
        for x in range(max(0, left), min(right, image.width)):
            _, _, _, alpha = image.getpixel((x, y))
            if alpha:
                image.putpixel((x, y), (red, green, blue, alpha))


def create_dark_background_variant(
    source: Path,
    target: Path,
    regions: list[tuple[int, int, int, int]],
    palette: list[tuple[int, int, int]],
) -> None:
    with Image.open(source) as current:
        image = current.convert("RGBA")
    for region in regions:
        silver_region(image, region, palette)
    image.save(target, optimize=True)


def main() -> None:
    ICONS.mkdir(parents=True, exist_ok=True)
    master = render_master()

    resize(master, (1024, 1024)).save(IMAGES / "logo-icon.png", optimize=True)
    padded_icon(master, 32, .84).save(ICONS / "favicon-32x32.png", optimize=True)
    padded_icon(master, 180, .82).save(ICONS / "apple-touch-icon.png", optimize=True)
    padded_icon(master, 192, .84).save(ICONS / "favicon-192x192.png", optimize=True)

    replace_region(
        IMAGES / "logo-horizontal.png",
        (0, 0, 315, 365),
        resize(master, (239, 239)),
        (48, 39),
    )
    replace_region(
        IMAGES / "logo-darkbg.png",
        (0, 0, 670, 455),
        resize(master, (420, 420)),
        (125, 5),
    )

    with Image.open(IMAGES / "logo-txt.png") as current:
        palette = time_silver_palette(current.convert("RGBA"))

    create_dark_background_variant(
        IMAGES / "logo-txt.png",
        IMAGES / "logo-txt-darkbg.png",
        [
            (0, 334, 650, 342),
            (650, 319, 1120, 365),
            (1120, 334, 1734, 342),
        ],
        palette,
    )
    create_dark_background_variant(
        IMAGES / "logo-horizontal.png",
        IMAGES / "logo-horizontal-darkbg.png",
        [
            (333, 334, 1000, 342),
            (1000, 319, 1450, 365),
            (1450, 334, 2114, 342),
        ],
        palette,
    )
    create_dark_background_variant(
        IMAGES / "logo-darkbg.png",
        IMAGES / "logo-stacked-darkbg.png",
        [
            (0, 570, 257, 574),
            (257, 564, 437, 582),
            (415, 570, 670, 574),
        ],
        palette,
    )

    print("D2-Icon, helle/dunkle Lockups und Favicons wurden erzeugt.")


if __name__ == "__main__":
    main()
