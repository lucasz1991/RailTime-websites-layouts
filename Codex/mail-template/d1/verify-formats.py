from email import policy
from email.parser import BytesParser
from pathlib import Path


HERE = Path(__file__).resolve().parent


def verify_eml(path: Path, expected_sender: str | None = None) -> list[str]:
    message = BytesParser(policy=policy.default).parsebytes(path.read_bytes())
    content_types = [part.get_content_type() for part in message.walk()]
    for expected in ("text/plain", "text/html", "image/png", "image/jpeg"):
        if expected not in content_types:
            raise SystemExit(f"{path.name} enthält {expected} nicht.")
    cids = {part.get("Content-ID") for part in message.walk() if part.get("Content-ID")}
    if not {"<railtime-logo>", "<railtime-hero>"}.issubset(cids):
        raise SystemExit(f"CID-Bilder fehlen in {path.name}.")
    if expected_sender and expected_sender not in str(message.get("From", "")):
        raise SystemExit(f"{path.name} enthält den Absender {expected_sender!r} nicht.")
    return content_types


def verify_outlook(path: Path, expected_values: tuple[str, ...] = ()) -> None:
    data = path.read_bytes()
    if data[:8] != bytes.fromhex("d0cf11e0a1b11ae1"):
        raise SystemExit(f"{path.name} ist kein gültiger OLE-Container.")
    common_values = (
        "{{BETREFF}} | RT Rail Time GmbH",
        "RT / KOMMUNIKATION",
        "logo-mail-dark.png",
        "hero-railtime.jpg",
        "railtime-logo",
        "railtime-hero",
    )
    for value in common_values + expected_values:
        if value.encode("utf-16le") not in data and value.encode("utf-8") not in data:
            raise SystemExit(f"{path.name} enthält {value!r} nicht.")


def verify_full_width_html(path: Path) -> None:
    html = path.read_text(encoding="utf-8")
    if 'width="100%"' not in html or "width:100%" not in html:
        raise SystemExit(f"{path.name} verwendet nicht die volle Breite.")
    for fixed_width in ('width="640"', "width:640px", 'width="600"', "width:600px"):
        if fixed_width in html:
            raise SystemExit(f"{path.name} enthält noch die feste Breite {fixed_width!r}.")


def main() -> None:
    required = [
        HERE / "universal" / "email-body.html",
        HERE / "universal" / "email-body.txt",
        HERE / "universal" / "RailTime-E-Mailvorlage.eml",
        HERE / "universal" / "signature-dark.html",
        HERE / "universal" / "signature-light.html",
        HERE / "outlook" / "RailTime-Signatur.htm",
        HERE / "outlook" / "RailTime-Signatur.rtf",
        HERE / "outlook" / "RailTime-Signatur.txt",
        HERE / "mazan" / "RailTime-Mazan-E-Mailvorlage.eml",
        HERE / "mazan" / "signature-dark.html",
        HERE / "mazan" / "signature-light.html",
        HERE / "mazan" / "outlook" / "RailTime-Signatur-Mazan.htm",
        HERE / "mazan" / "outlook" / "RailTime-Signatur-Mazan.rtf",
        HERE / "mazan" / "outlook" / "RailTime-Signatur-Mazan.txt",
    ]
    missing = [str(path.relative_to(HERE)) for path in required if not path.is_file()]
    if missing:
        raise SystemExit("Fehlende Dateien: " + ", ".join(missing))

    for path in (
        HERE / "source" / "email-master.html",
        HERE / "source" / "signature-dark-master.html",
        HERE / "source" / "signature-light-master.html",
        HERE / "universal" / "email-body.html",
        HERE / "universal" / "signature-dark.html",
        HERE / "universal" / "signature-light.html",
        HERE / "outlook" / "RailTime-E-Mailvorlage.html",
        HERE / "outlook" / "RailTime-Signatur.htm",
        HERE / "mazan" / "email-body.html",
        HERE / "mazan" / "signature-dark.html",
        HERE / "mazan" / "signature-light.html",
        HERE / "mazan" / "outlook" / "RailTime-Mazan-E-Mailvorlage.html",
        HERE / "mazan" / "outlook" / "RailTime-Signatur-Mazan.htm",
    ):
        verify_full_width_html(path)

    content_types = verify_eml(HERE / "universal" / "RailTime-E-Mailvorlage.eml")
    verify_eml(HERE / "mazan" / "RailTime-Mazan-E-Mailvorlage.eml", "Mazan")

    for filename in ("RailTime-E-Mailvorlage.oft", "RailTime-E-Mailvorlage.msg"):
        path = HERE / "outlook" / filename
        if not path.is_file():
            raise SystemExit(f"{filename} fehlt.")
        verify_outlook(path)
    for filename in ("RailTime-Mazan-E-Mailvorlage.oft", "RailTime-Mazan-E-Mailvorlage.msg"):
        path = HERE / "mazan" / "outlook" / filename
        if not path.is_file():
            raise SystemExit(f"{filename} fehlt.")
        verify_outlook(path, ("Mazan", "kontakt@rail-time.de"))
    print("Mail-Paket valide:", ", ".join(content_types), "+ OFT + MSG + Mazan")


if __name__ == "__main__":
    main()
