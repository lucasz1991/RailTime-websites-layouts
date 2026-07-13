from __future__ import annotations

import subprocess
import tempfile
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

    print("D2-Icon, globale Lockups und Favicons wurden erzeugt.")


if __name__ == "__main__":
    main()
