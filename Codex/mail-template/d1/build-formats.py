from __future__ import annotations

import base64
import re
import shutil
import textwrap
from email import policy
from email.message import EmailMessage
from pathlib import Path

from PIL import Image, ImageOps


HERE = Path(__file__).resolve().parent
PROJECT = HERE.parents[2]
IMAGES = PROJECT / "Shared" / "assets" / "images"
SOURCE = HERE / "source"
ASSETS = HERE / "assets"
UNIVERSAL = HERE / "universal"
OUTLOOK = HERE / "outlook"
OUTLOOK_FILES = OUTLOOK / "RailTime-Signatur_files"
MAZAN = HERE / "mazan"
MAZAN_ASSETS = MAZAN / "assets"
MAZAN_OUTLOOK = MAZAN / "outlook"
MAZAN_OUTLOOK_FILES = MAZAN_OUTLOOK / "RailTime-Signatur-Mazan_files"

MAZAN_PROFILE = {
    "VORNAME_NACHNAME": "Mazan",
    "POSITION": "RT Rail Time GmbH",
    "DURCHWAHL": "0160 1881848",
    "DURCHWAHL_TEL": "+491601881848",
    "E_MAIL": "kontakt@rail-time.de",
}


PLAIN_BODY = """{{ANREDE}},

{{KURZE_EINLEITUNG}}

{{NACHRICHT}}

EINSATZDATEN / OPTIONAL
Einsatzort: {{EINSATZORT}}
Zeitraum: {{ZEITRAUM}}
Leistung: {{LEISTUNG}}
Ansprechpartner: {{ANSPRECHPARTNER}}

{{CTA_TEXT}}: {{CTA_URL}}

Freundliche Grüße
{{VORNAME_NACHNAME}}
{{POSITION}}

RT Rail Time GmbH
Borsteler Weg 29–31 · 21423 Winsen (Luhe)
T {{DURCHWAHL}} · M {{MOBIL}}
E {{E_MAIL}}
Notfalldienst 24/7: 0160 1881848

Geschäftsführung: {{GESCHAEFTSFUEHRUNG}}
Registergericht: {{REGISTERGERICHT}} · HRB {{HRB}}
USt-IdNr.: {{UST_ID}}
"""


PLAIN_SIGNATURE = """{{VORNAME_NACHNAME}}
{{POSITION}}

RT Rail Time GmbH
Borsteler Weg 29–31
21423 Winsen (Luhe)

T {{DURCHWAHL}}
M {{MOBIL}}
E {{E_MAIL}}
Notfalldienst 24/7: 0160 1881848
Zentrale E-Mail: kontakt@rail-time.de

Geschäftsführung: {{GESCHAEFTSFUEHRUNG}}
Registergericht: {{REGISTERGERICHT}} · HRB {{HRB}}
USt-IdNr.: {{UST_ID}}
"""


MAZAN_SIGNATURE = """Mazan
RT Rail Time GmbH

Borsteler Weg 29–31
21423 Winsen (Luhe)

T 0160 1881848
E kontakt@rail-time.de
Notfalldienst 24/7: 0160 1881848

Geschäftsführung: {{GESCHAEFTSFUEHRUNG}}
Registergericht: {{REGISTERGERICHT}} · HRB {{HRB}}
USt-IdNr.: {{UST_ID}}
"""


def render_logo(source: Path, target: Path, width: int) -> None:
    with Image.open(source) as image:
        image = image.convert("RGBA")
        height = round(image.height * width / image.width)
        image.resize((width, height), Image.Resampling.LANCZOS).save(target, optimize=True)


def render_hero(source: Path, target: Path) -> None:
    with Image.open(source) as image:
        image = ImageOps.fit(image.convert("RGB"), (1280, 520), method=Image.Resampling.LANCZOS, centering=(0.5, 0.48))
        image.save(target, quality=82, optimize=True, progressive=True)


def substitute(template: str, **values: str) -> str:
    for key, value in values.items():
        template = template.replace("{{" + key + "}}", value)
    return template


def inline_image(path: Path) -> str:
    return "data:image/png;base64," + base64.b64encode(path.read_bytes()).decode("ascii")


def without_mobile_row(template: str) -> str:
    return re.sub(r"^[ \t]*M&nbsp;.*?\{\{MOBIL\}\}</a><br>\s*$", "", template, flags=re.MULTILINE)


def rtf_escape(value: str) -> str:
    result: list[str] = []
    for character in value:
        code = ord(character)
        if character in "\\{}":
            result.append("\\" + character)
        elif code < 128:
            result.append(character)
        else:
            if code > 32767:
                code -= 65536
            result.append(f"\\u{code}?")
    return "".join(result)


def build_rtf(logo_path: Path, target: Path, profile: dict[str, str] | None = None) -> None:
    with Image.open(logo_path) as logo:
        width, height = logo.size
    png_hex = "\n".join(textwrap.wrap(logo_path.read_bytes().hex(), 128))
    goal_width = 4500
    goal_height = round(goal_width * height / width)
    profile = profile or {
        "VORNAME_NACHNAME": "{{VORNAME_NACHNAME}}",
        "POSITION": "{{POSITION}}",
        "DURCHWAHL": "{{DURCHWAHL}}",
        "MOBIL": "{{MOBIL}}",
        "E_MAIL": "{{E_MAIL}}",
    }
    phones = f"T {profile['DURCHWAHL']}"
    if profile.get("MOBIL"):
        phones += f"\\tab M {profile['MOBIL']}"
    phones_rtf = rtf_escape(phones).replace("\\\\tab", "\\tab")
    lines = [
        r"{\rtf1\ansi\ansicpg1252\deff0",
        r"{\fonttbl{\f0 Arial;}{\f1 Courier New;}}",
        r"{\colortbl;\red17\green24\blue32;\red228\green0\blue43;\red90\green102\blue113;}",
        rf"\pard\sa140{{\pict\pngblip\picw{width}\pich{height}\picwgoal{goal_width}\pichgoal{goal_height}\n{png_hex}}}\par",
        rf"\pard\f0\fs34\b {rtf_escape(profile['VORNAME_NACHNAME'])}\b0\par",
        rf"\pard\f1\fs18\cf2\b {rtf_escape(profile['POSITION'])}\b0\cf0\par",
        r"\pard\sa110\brdrt\brdrs\brdrw12\brdrcf2\par",
        rf"\pard\f0\fs20 {phones_rtf}\par",
        rf"\pard\f0\fs20 E {rtf_escape(profile['E_MAIL'])}\par",
        rf"\pard\f0\fs18\cf3 {rtf_escape('RT Rail Time GmbH · Borsteler Weg 29–31 · 21423 Winsen (Luhe)')}\par",
        rf"\pard\f1\fs18\cf2\b {rtf_escape('24/7 NOTFALLDIENST · 0160 1881848')}\b0\cf0\par",
        rf"\pard\f0\fs14\cf3 {rtf_escape('Geschäftsführung: {{GESCHAEFTSFUEHRUNG}} · Registergericht: {{REGISTERGERICHT}} · HRB {{HRB}} · USt-IdNr. {{UST_ID}}')}\cf0\par",
        "}",
    ]
    target.write_text("\n".join(lines), encoding="ascii")


def build_eml(
    html: str,
    logo_path: Path,
    hero_path: Path,
    target: Path,
    plain_body: str = PLAIN_BODY,
    from_header: str = "{{VORNAME_NACHNAME}} <{{E_MAIL}}>",
) -> None:
    message = EmailMessage(policy=policy.SMTP)
    message["Subject"] = "{{BETREFF}} | RT Rail Time GmbH"
    message["From"] = from_header
    message["To"] = "{{EMPFAENGER_E_MAIL}}"
    message.set_content(plain_body, charset="utf-8")
    message.add_alternative(html, subtype="html", charset="utf-8")
    html_part = message.get_payload()[-1]
    html_part.add_related(
        logo_path.read_bytes(),
        maintype="image",
        subtype="png",
        cid="<railtime-logo>",
        filename="logo-mail-dark.png",
        disposition="inline",
    )
    html_part.add_related(
        hero_path.read_bytes(),
        maintype="image",
        subtype="jpeg",
        cid="<railtime-hero>",
        filename="hero-railtime.jpg",
        disposition="inline",
    )
    target.write_bytes(message.as_bytes())


def main() -> None:
    for directory in (
        ASSETS,
        UNIVERSAL,
        OUTLOOK,
        OUTLOOK_FILES,
        MAZAN,
        MAZAN_ASSETS,
        MAZAN_OUTLOOK,
        MAZAN_OUTLOOK_FILES,
    ):
        directory.mkdir(parents=True, exist_ok=True)

    logo_dark = ASSETS / "logo-mail-dark.png"
    logo_light = ASSETS / "logo-signature-light.png"
    logo_signature_dark = ASSETS / "logo-signature-dark.png"
    hero = ASSETS / "hero-railtime.jpg"
    render_logo(IMAGES / "logo-horizontal-darkbg.png", logo_dark, 760)
    render_logo(IMAGES / "logo-horizontal-darkbg.png", logo_signature_dark, 620)
    render_logo(IMAGES / "logo-horizontal.png", logo_light, 620)
    render_hero(IMAGES / "s3.jpg", hero)
    shutil.copy2(logo_signature_dark, OUTLOOK_FILES / "logo.png")

    email_master = (SOURCE / "email-master.html").read_text(encoding="utf-8")
    email_relative = substitute(email_master, LOGO_SRC="../assets/logo-mail-dark.png", HERO_SRC="../assets/hero-railtime.jpg")
    email_cid = substitute(email_master, LOGO_SRC="cid:railtime-logo", HERO_SRC="cid:railtime-hero")
    (UNIVERSAL / "email-body.html").write_text(email_relative, encoding="utf-8")
    (UNIVERSAL / "email-body-cid.html").write_text(email_cid, encoding="utf-8")
    (UNIVERSAL / "email-body.txt").write_text(PLAIN_BODY, encoding="utf-8")
    (OUTLOOK / "RailTime-E-Mailvorlage.html").write_text(email_cid, encoding="utf-8")
    build_eml(email_cid, logo_dark, hero, UNIVERSAL / "RailTime-E-Mailvorlage.eml")

    dark_master = (SOURCE / "signature-dark-master.html").read_text(encoding="utf-8")
    light_master = (SOURCE / "signature-light-master.html").read_text(encoding="utf-8")
    (UNIVERSAL / "signature-dark.html").write_text(
        substitute(dark_master, LOGO_SRC="../assets/logo-signature-dark.png"), encoding="utf-8"
    )
    (UNIVERSAL / "signature-light.html").write_text(
        substitute(light_master, LOGO_SRC="../assets/logo-signature-light.png"), encoding="utf-8"
    )
    (UNIVERSAL / "signature-dark-inline.html").write_text(
        substitute(dark_master, LOGO_SRC=inline_image(logo_signature_dark)), encoding="utf-8"
    )
    (UNIVERSAL / "signature-light-inline.html").write_text(
        substitute(light_master, LOGO_SRC=inline_image(logo_light)), encoding="utf-8"
    )
    (UNIVERSAL / "signature.txt").write_text(PLAIN_SIGNATURE, encoding="utf-8")

    outlook_signature = substitute(dark_master, LOGO_SRC="RailTime-Signatur_files/logo.png")
    (OUTLOOK / "RailTime-Signatur.htm").write_text(outlook_signature, encoding="utf-8")
    (OUTLOOK / "RailTime-Signatur.txt").write_text(PLAIN_SIGNATURE, encoding="utf-8")
    build_rtf(OUTLOOK_FILES / "logo.png", OUTLOOK / "RailTime-Signatur.rtf")

    for source_asset in (logo_dark, logo_signature_dark, logo_light, hero):
        shutil.copy2(source_asset, MAZAN_ASSETS / source_asset.name)
    shutil.copy2(logo_signature_dark, MAZAN_OUTLOOK_FILES / "logo.png")

    mazan_email_master = substitute(email_master, **MAZAN_PROFILE)
    mazan_email_master = without_mobile_row(mazan_email_master)
    mazan_email_relative = substitute(
        mazan_email_master,
        LOGO_SRC="assets/logo-mail-dark.png",
        HERO_SRC="assets/hero-railtime.jpg",
    )
    mazan_email_cid = substitute(
        mazan_email_master,
        LOGO_SRC="cid:railtime-logo",
        HERO_SRC="cid:railtime-hero",
    )
    mazan_plain_body = substitute(PLAIN_BODY, **MAZAN_PROFILE).replace("M {{MOBIL}}\n", "")
    (MAZAN / "email-body.html").write_text(mazan_email_relative, encoding="utf-8")
    (MAZAN / "email-body.txt").write_text(mazan_plain_body, encoding="utf-8")
    (MAZAN_OUTLOOK / "RailTime-Mazan-E-Mailvorlage.html").write_text(mazan_email_cid, encoding="utf-8")
    build_eml(
        mazan_email_cid,
        MAZAN_ASSETS / "logo-mail-dark.png",
        MAZAN_ASSETS / "hero-railtime.jpg",
        MAZAN / "RailTime-Mazan-E-Mailvorlage.eml",
        plain_body=mazan_plain_body,
        from_header="Mazan <kontakt@rail-time.de>",
    )

    mazan_dark = without_mobile_row(substitute(dark_master, **MAZAN_PROFILE))
    mazan_light = without_mobile_row(substitute(light_master, **MAZAN_PROFILE))
    (MAZAN / "signature-dark.html").write_text(
        substitute(mazan_dark, LOGO_SRC="assets/logo-signature-dark.png"), encoding="utf-8"
    )
    (MAZAN / "signature-light.html").write_text(
        substitute(mazan_light, LOGO_SRC="assets/logo-signature-light.png"), encoding="utf-8"
    )
    (MAZAN / "signature-dark-inline.html").write_text(
        substitute(mazan_dark, LOGO_SRC=inline_image(MAZAN_ASSETS / "logo-signature-dark.png")), encoding="utf-8"
    )
    (MAZAN / "signature-light-inline.html").write_text(
        substitute(mazan_light, LOGO_SRC=inline_image(MAZAN_ASSETS / "logo-signature-light.png")), encoding="utf-8"
    )
    (MAZAN / "signature.txt").write_text(MAZAN_SIGNATURE, encoding="utf-8")
    (MAZAN_OUTLOOK / "RailTime-Signatur-Mazan.htm").write_text(
        substitute(mazan_dark, LOGO_SRC="RailTime-Signatur-Mazan_files/logo.png"), encoding="utf-8"
    )
    (MAZAN_OUTLOOK / "RailTime-Signatur-Mazan.txt").write_text(MAZAN_SIGNATURE, encoding="utf-8")
    build_rtf(
        MAZAN_OUTLOOK_FILES / "logo.png",
        MAZAN_OUTLOOK / "RailTime-Signatur-Mazan.rtf",
        profile={**MAZAN_PROFILE, "MOBIL": ""},
    )

    print("HTML, TXT, RTF, EML und Mail-Assets inklusive Mazan-Variante wurden erzeugt.")


if __name__ == "__main__":
    main()
