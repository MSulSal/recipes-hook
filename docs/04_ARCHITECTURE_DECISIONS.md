# Architecture Decisions

## Plugin Instead of Theme-Only Build

Recipe behavior belongs in a custom plugin so the client can change themes later without losing recipe PDFs, search behavior, or admin fields.

## Custom Post Type for Recipe PDFs

Each recipe will be represented as a `recipe_pdf` post. This keeps recipes manageable in WordPress admin while allowing titles, categories, tags, archive pages, and single recipe pages.

## Search

Search begins with reliable WordPress data: title, PDF filename, categories, and tags. The shortcode loads published recipe posts and applies a simple PHP-side match, which is appropriate for this small client site and avoids fragile custom SQL. Optional PDF text extraction may be added only if it stays portable and fallback-safe.

## PDF Text Extraction Tradeoffs

PDF text extraction is useful for machine-readable PDFs but unreliable for scanned/image PDFs. The site must work even when extraction fails.

## Security Decisions

The plugin uses nonces, capability checks, PDF MIME validation, sanitization, and escaping for the PDF field. Raw SQL will be avoided unless there is a clear reason.

## Simplicity

The client has a small budget and asked for a straightforward recipe website. This project intentionally avoids complex recipe schemas, nutrition fields, SPAs, paid plugins, and heavy theme work.

## WordPress Admin Ownership

The client should be able to do all routine work from WordPress admin: upload PDFs, title recipes, set categories/tags, publish/unpublish, replace files, manage pages, and update navigation. GitHub and code are for development/handoff only.

## 60 MB Import Limit

The custom behavior is isolated in a plugin so it can be uploaded separately if a hosting import limit blocks a full LocalWP export. The repo does not track uploads, which keeps the code package small and portable.
