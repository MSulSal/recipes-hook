# Architecture Decisions

## Plugin Instead of Theme-Only Build

Recipe behavior belongs in a custom plugin so the client can change themes later without losing recipe PDFs, search behavior, or admin fields.

## Theme and Plugin Separation

The `recipes-hook-theme` theme owns presentation: home library layout, manage page layout, login page styling, and single templates.
The `recipe-pdf-library` plugin owns business behavior: data model, PDF attachment handling, indexing, search rules, and frontend CRUD action handlers.

## User Collections

The management experience is account-scoped. Logged-in users query and manage only their own recipes from home/manage views, but each recipe can be marked Public or Private. Signed-out users can browse only Public recipes.

## Custom Post Type for Recipe PDFs

Each recipe will be represented as a `recipe_pdf` post. This keeps recipes manageable in WordPress admin while allowing titles, categories, tags, archive pages, and single recipe pages.

Recipe detail URLs use the singular `/recipe/` slug. The post type archive route exists (`/recipe-archive/`) but is redirected to home because this project uses a custom home library experience instead of archive navigation.

## Search

Search begins with reliable WordPress data: title, PDF filename, categories, and tags. The shortcode loads recipe posts based on session context (signed-in: own private/public; signed-out: public only) and applies a simple PHP-side match, which is appropriate for this small client site and avoids fragile custom SQL. Optional PDF text extraction may be added only if it stays portable and fallback-safe.

## PDF Text Extraction Tradeoffs

PDF text extraction is useful for machine-readable PDFs but unreliable for scanned/image PDFs. The plugin uses a small fallback-safe extractor instead of a Composer dependency so the plugin remains easy to zip and upload on hosts with small import limits. Extraction failure does not block saving or displaying a recipe; it only means text inside that PDF may not be searchable.

## Security Decisions

The plugin uses nonces, capability checks, PDF MIME validation, sanitization, and escaping for the PDF field. Raw SQL will be avoided unless there is a clear reason.

## Simplicity

The client has a small budget and asked for a straightforward recipe website. This project intentionally avoids complex recipe schemas, nutrition fields, SPAs, paid plugins, and heavy theme work.

## WordPress Admin Ownership

The client should be able to do all routine work from WordPress admin: upload PDFs, title recipes, set categories/tags, replace files, manage pages, and update navigation. GitHub and code are for development/handoff only.

## 60 MB Import Limit

The custom behavior is isolated in a plugin so it can be uploaded separately if a hosting import limit blocks a full LocalWP export. The repo does not track uploads, which keeps the code package small and portable.

## Styling

Frontend CSS is scoped under `rpl-`/`rht-` classes in the custom theme and does not use external CDNs. Home supports both gallery and list views with PDF preview thumbnails where WordPress can generate them.

## Migration Strategy

The plugin is intentionally self-contained and small. If a host refuses imports over 60 MB, upload the plugin zip separately and migrate database/media in smaller steps. The client still manages all recipe content through WordPress admin after migration.
