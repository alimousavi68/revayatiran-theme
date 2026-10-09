# CSS source and delivery

Keep editing `modules/*.css` and `input.css` for utility/component rules. Frontend
requests load committed `dist/core.min.css`, one view bundle and, for guests,
`dist/auth-dialog.min.css`. `main-custom.css` is a source index, not a frontend
stylesheet. The root `style.css` remains WordPress theme metadata.

From the theme root:

```sh
node assets/css/build-css.mjs
node assets/css/build-css.mjs --check
```

`utilities.css` contains the existing Tailwind reset/utilities/component output,
separated from the historical compiled module prefix in `style.css` without
purging utility classes. `input.css` no longer imports the module index. If
utility classes change, regenerate `utilities.css` with the existing Tailwind
3.4.19 CLI and this theme's `tailwind.config.js`, then run the delivery build.
For the existing local installation (no install is needed), from the theme root:

```sh
node '../../../orginal source/node_modules/tailwindcss/lib/cli.js' -c tailwind.config.js -i assets/css/input.css -o assets/css/utilities.css --minify
node assets/css/build-css.mjs
```

The delivery build itself needs only Node; it does not require the original
source folder or Tailwind on the hosting server. The old `style.css` in assets
is a historical compiled file and is no longer enqueued.

The build uses only Node's standard library. It preserves rule order, strings,
escapes, meaningful spaces and calc operators; it removes ordinary comments and
reduces whitespace without selector/declaration rewriting or purging dynamic
classes. Relative URLs are validated and relocated. Each output has a content
hash used by WordPress as its cache version. Unchanged outputs are not rewritten.

`inc/css-bundles.php` selects views. Bundle source lists/size/hash are recorded in
`dist/manifest.json`; `manifest.php` is the small runtime map. No compilation or
directory scan runs during a page request. Commit generated files with source
changes; the deployment packager checks build freshness before making ZIPs.

Core contains utilities and the shared variables/header prefix. View bundles
retain the remaining shared rules in their original relative order alongside
the required features. This intentionally keeps some common rules in multiple
view outputs to preserve the cascade; moving all shared rules ahead of page
rules would change responsive overrides. Do not merge/remove selectors solely
based on static template searches: Customizer, PHP and JS generate classes.

`legacy-compat.css` preserves the historical compiled prefix's declarations that
are not superseded by current modules. The historical full prefix is not shipped
to the browser. Global typography is now in `modules/typography.css`, and shared
card surfaces in `modules/layout-shared.css`, rather than in feature-only files.
Compatibility declarations were compared by selector, at-rule context, property
and importance using the already installed PostCSS parser; no parser dependency
is needed for normal delivery builds or on the server.

Icon/font libraries and Swiper remain independently cached assets. The body
stylesheet used by the block editor is still a source module, outside this
frontend delivery pipeline. The old public style handles used by Customizer
remain available; custom CSS stays after the selected view bundle.
