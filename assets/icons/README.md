# Phosphor subset

Production enqueues `dist/icons.css` and three subset WOFF2 fonts. Original Phosphor sources remain local build inputs; the full fonts/styles are not downloaded by visitors. Existing `ph`, `ph-bold`, `ph-fill` classes and glyph metrics remain compatible.

The builder scans PHP/JS in the theme and companion plugin, including names in ternary expressions and dynamic string-built classes. It conservatively includes every discovered name in each used weight, so hidden/mobile/auth/role states keep working. Unknown icon names fail the build. Arbitrary database-authored icon names outside code are not automatically included.

After adding/removing/changing icon classes, from theme root:

```sh
python3 assets/icons/build-icons.py
python3 assets/icons/build-icons.py --check
```

Building needs Python + FontTools with WOFF2 support (`fonttools[woff]==4.60.1`). This is an offline build dependency, never installed or run on the web host. `--check` uses only Python standard library and validates icon usage in source, CSS and committed font hashes; unrelated text/PHP edits do not force a rebuild. Current agent used tools installed in `/private/tmp/revayat-font-build-tools`; no runtime dependency added.

Original Phosphor MIT license is included in `LICENSE`; family/copyright metadata is retained in each subset font. The deployment ZIP excludes the full `assets/vendor/phosphor/` library; those files remain local offline build inputs. Build outputs preserve family names/cmap, outlines and metrics; only unused glyphs are removed. Fonts use `font-display:swap` and content-versioned URLs.
