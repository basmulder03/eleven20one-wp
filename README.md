# Eleven20one — WordPress plugin & theme

The custom WordPress plugin and block theme built for
[eleven20one.nl](https://eleven20one.nl), a Dutch cover band's website.
Extracted here so the code can be reused, reviewed, or dropped into
another WordPress site independently of the band's own content and
deployment setup.

## What's here

- **`eleven20one-core/`** — the plugin. Custom post types for shows,
  band members, and portfolio entries; a show countdown (with a
  publishable `.ics` calendar feed); FAQ schema; and MusicGroup/Event
  structured data via a Yoast SEO filter. Requires PHP 8.0+ and the
  free [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/)
  plugin.
- **`eleven20one/`** — a lightweight custom block theme (not a child
  theme) built around a dark anthracite/purple palette, styled for the
  post types above.

## Using it elsewhere

Both directories are self-contained — copy `eleven20one-core/` into
`wp-content/plugins/` and `eleven20one/` into `wp-content/themes/` on
any WordPress 6.5+ site. The theme/plugin's copy (colors, band-specific
strings) is Eleven20one's; swap it for your own content after
activating.

## Releases

Tagging `vX.Y.Z` builds and publishes a zip of each directory as a
GitHub Release — see
[`.github/workflows/release.yml`](.github/workflows/release.yml).

## License

GPL-2.0-or-later — see [`LICENSE`](LICENSE).
