# Recipe PDF Library

A small WordPress project for a simple recipe PDF library. The client can upload recipe PDFs in WordPress, give them titles/categories/tags, and show visitors a searchable recipe library without retyping ingredients or instructions.

## Features

- Custom WordPress plugin: `Recipe PDF Library`
- Recipe custom post type for PDF-based recipes
- Planned searchable frontend library with shortcode: `[recipe_pdf_library]`
- Search by title, PDF filename, category, and tag
- Fallback-safe PDF text indexing for some machine-readable PDFs
- Planned in-browser PDF view and download links
- Client handoff documentation in `docs/`
- Routine client management through WordPress admin only

## Local Development

This repo is initialized at the LocalWP site root. WordPress core, uploads, caches, LocalWP config, secrets, and bundled themes/plugins are ignored.

Tracked project code is expected to live at:

`app/public/wp-content/plugins/recipe-pdf-library/`

## Handoff Notes

The final site is intended to be easy for a nontechnical client to own: WordPress admin for content, a portable custom plugin for recipe behavior, and documentation for migration and routine maintenance.

The plugin is intentionally separate from uploads and theme files so it can be zipped and installed on hosting separately if a host only accepts small imports.

PDF text search is best-effort. Scanned/image PDFs may need descriptive titles, categories, and tags.

## Screenshots

Add screenshots here after the demo page is populated.
