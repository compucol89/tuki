#!/usr/bin/env python3
"""Genera el subset de Font Awesome Solid usado por el frontend público.

Uso:
    python3 scripts/build-fa-subset.py

Requiere fonttools (`pip install fonttools`). Escanea las vistas públicas,
servicios y JS del frontend, resuelve los codepoints desde el CSS de FA,
escribe public/webfonts/fa-solid-front.woff2 y genera
resources/css/fontawesome-front.css con solo los iconos usados.

Si se agrega un icono nuevo en una vista pública hay que volver a correrlo.
"""
import pathlib
import re
import subprocess
import sys

ROOT = pathlib.Path(__file__).resolve().parent.parent
SCAN_PATHS = [
    ROOT / "resources/views/frontend",
    ROOT / "app/Services",
    ROOT / "resources/js",
    ROOT / "public/assets/front/js/script.js",
]
CSS_FILES = [
    ROOT / "node_modules/@fortawesome/fontawesome-free/css/fontawesome.css",
    ROOT / "node_modules/@fortawesome/fontawesome-free/css/brands.css",
    ROOT / "node_modules/@fortawesome/fontawesome-free/css/regular.css",
]

ICON_RE = re.compile(r"fa-[a-z0-9-]+")
ICON_CP_RE = re.compile(r'\.(fa-[a-z0-9-]+)\s*\{[^}]*?--fa:\s*"\\([0-9a-fA-F]+)"')


def collect_icons() -> set[str]:
    icons: set[str] = set()
    for path in SCAN_PATHS:
        if path.is_file():
            files = [path]
        elif path.is_dir():
            files = [p for p in path.rglob("*") if p.suffix in {".php", ".js", ".blade.php"} or p.is_file()]
        else:
            continue
        for file in files:
            try:
                icons.update(ICON_RE.findall(file.read_text(encoding="utf-8", errors="ignore")))
            except OSError:
                continue
    return {i for i in icons if i not in {"fa-solid-900", "fa-solid-front"}}


def icon_map() -> dict[str, str]:
    css = "\n".join(p.read_text(encoding="utf-8") for p in CSS_FILES if p.exists())
    mapping: dict[str, str] = {}
    for cls, cp in ICON_CP_RE.findall(css):
        mapping.setdefault(cls, cp.lower())
    return mapping


def subset_font(source: pathlib.Path, target: pathlib.Path, codepoints: list[str]) -> None:
    subprocess.run(
        [
            sys.executable,
            "-m",
            "fontTools.subset",
            str(source),
            "--unicodes=" + ",".join("U+" + c.upper() for c in codepoints),
            "--flavor=woff2",
            "--no-hinting",
            "--desubroutinize",
            "--output-file=" + str(target),
        ],
        check=True,
    )
    print(f"{target.name} ({target.stat().st_size} bytes)")


def build_front_css(used: set[str], mapping: dict[str, str]) -> None:
    """Genera el CSS del frontend con la base de FA y solo los iconos usados."""
    core = (ROOT / "node_modules/@fortawesome/fontawesome-free/css/fontawesome.css").read_text(encoding="utf-8")

    # La base es todo el CSS core menos las reglas de iconos (las que definen --fa).
    base = re.sub(r"[^{}]*\{[^{}]*--fa:[^{}]*\}", "", core)
    base = re.sub(r"\n{3,}", "\n\n", base).strip()

    faces = """@font-face {
  font-family: 'Font Awesome 6 Free';
  font-style: normal;
  font-weight: 900;
  font-display: block;
  src: url('/webfonts/fa-solid-front.woff2') format('woff2');
}

@font-face {
  font-family: 'Font Awesome 6 Free';
  font-style: normal;
  font-weight: 400;
  font-display: block;
  src: url('/webfonts/fa-regular-front.woff2') format('woff2');
}

@font-face {
  font-family: 'Font Awesome 6 Brands';
  font-style: normal;
  font-weight: 400;
  font-display: block;
  src: url('/webfonts/fa-brands-front.woff2') format('woff2');
}
"""

    icon_rules = "\n".join(
        f'.{cls}{{--fa:"\\{mapping[cls]}"}}'
        for cls in sorted(used)
        if cls in mapping
    )

    target = ROOT / "resources/css/fontawesome-front.css"
    target.write_text(
        "/* Generado por scripts/build-fa-subset.py. No editar a mano. */\n"
        + base
        + "\n\n"
        + faces
        + "\n"
        + icon_rules
        + "\n",
        encoding="utf-8",
    )
    print(f"CSS frontend -> {target.relative_to(ROOT)} ({target.stat().st_size} bytes)")


def main() -> int:
    used = collect_icons()
    mapping = icon_map()
    missing = sorted(i for i in used if i not in mapping)
    codepoints = sorted({mapping[i] for i in used if i in mapping}, key=lambda c: int(c, 16))

    if missing:
        print("Aviso: iconos sin glifo en FA Free:", ", ".join(missing), file=sys.stderr)
    if not codepoints:
        print("No se encontraron iconos para incluir.", file=sys.stderr)
        return 1

    # Subset de las tres familias. Se pasan todos los codepoints usados; cada
    # fuente incluye solo los glifos que realmente tiene.
    subset_font(
        ROOT / "public/webfonts/fa-solid-900.woff2",
        ROOT / "public/webfonts/fa-solid-front.woff2",
        codepoints,
    )
    subset_font(
        ROOT / "public/webfonts/fa-regular-400.woff2",
        ROOT / "public/webfonts/fa-regular-front.woff2",
        codepoints,
    )
    subset_font(
        ROOT / "public/webfonts/fa-brands-400.woff2",
        ROOT / "public/webfonts/fa-brands-front.woff2",
        codepoints,
    )
    build_front_css(used, mapping)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
