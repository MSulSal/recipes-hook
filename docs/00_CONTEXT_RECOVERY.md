# Context Recovery

## What This Project Is

This is a small WordPress recipe PDF library for an Upwork client job titled "Recipe Website." The client wants to upload recipe PDFs instead of retyping recipes, then let visitors browse and search them.

## LocalWP Assumptions

- LocalWP project root: `C:\Users\Sul\Local Sites\reciperepo`
- WordPress root: `app/public`
- Custom plugin path: `app/public/wp-content/plugins/recipe-pdf-library/`
- The active theme is expected to remain a normal WordPress theme; recipe functionality belongs in the plugin.

## Git/Repo Status

- Local git repo initialized at the LocalWP project root.
- GitHub repo: `https://github.com/MSulSal/recipes-hook`
- WordPress core, uploads, caches, LocalWP config/logs, secrets, and third-party themes/plugins are ignored.

## Plugin Location

`app/public/wp-content/plugins/recipe-pdf-library/`

## Current Feature Status

- Commit 1 complete: repo and docs scaffold.
- Commit 2 complete: plugin scaffold added with `recipe_pdf` post type, `recipe_category`, and `recipe_tag`.
- Commit 3 complete: PDF upload/select field added to recipe edit screen with PDF validation, nonce/capability checks, admin columns, and filename-to-title helper.
- Commit 4 complete: `[recipe_pdf_library]` shortcode added with search, category filter, cards, empty state, no-results state, and scoped CSS.
- Commit 5 complete: single recipe pages display PDF actions, browser PDF iframe, categories/tags, graceful missing-PDF message, and back link.
- Commit 6 complete: fallback-safe PDF text indexing added with no Composer dependency. It stores extracted text in hidden post meta and keeps title/filename/category/tag search as the reliable baseline.
- Commit 7 complete: shortcode and detail page CSS polished for a cleaner responsive demo.
- Commit 8 complete: final documentation and proposal notes.
- The client workflow remains inside WordPress admin.

## Commands Run

- `git init`
- `gh auth status`
- `gh repo view MSulSal/recipes-hook --json url,nameWithOwner`
- `git remote add origin https://github.com/MSulSal/recipes-hook.git`
- PHP lint via LocalWP PHP executable for plugin PHP files
- `git diff --check`
- `git push`

## Decisions Made

- Git repo is rooted at the LocalWP project folder, not `app/public`, so top-level docs can be tracked cleanly.
- Recipe functionality will be implemented as a portable custom plugin.
- WordPress core and generated/local files will not be committed.
- Client recipe management should happen entirely through WordPress admin.
- Keep plugin portable so it can be zipped/uploaded separately if a host has a 60 MB import limit.
- PDF uploads use the WordPress Media Library from wp-admin, so the client does not need FTP or code access.

## Known Issues

- None yet.

## Next Recommended Step

Manual WordPress QA: activate plugin, create the Recipes page with `[recipe_pdf_library]`, upload the sample PDF, publish a recipe, and test search/view/download/mobile.

## Latest Commit Summary

- `chore: initialize WordPress project repo and documentation`
- `feat: scaffold recipe PDF library plugin`
- `feat: add PDF upload field for recipes`
- `feat: add recipe library shortcode with search and filters`
- `feat: add recipe detail PDF viewer`
- `feat: index PDF text for recipe search`
- `style: polish recipe library layout for client demo`
- `docs: finalize client handoff and proposal notes`
