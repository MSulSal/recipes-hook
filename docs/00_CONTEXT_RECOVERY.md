# Context Recovery

## What This Project Is

This is a small WordPress recipe PDF library for an Upwork client job titled "Recipe Website." The client wants to upload recipe PDFs instead of retyping recipes, then let visitors browse and search them.

## LocalWP Assumptions

- LocalWP project root: `C:\Users\Sul\Local Sites\reciperepo`
- WordPress root: `app/public`
- Custom plugin path: `app/public/wp-content/plugins/recipe-pdf-library/`
- Custom theme path: `app/public/wp-content/themes/recipes-hook-theme/`

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
- Commit 9 complete: UI-first polish pass on library and detail view markup/CSS.
- Commit 10 complete: presentation moved to custom theme and business logic kept in plugin.
- Commit 11 complete: authenticated frontend recipe create/edit/delete UI and action handlers.
- Commit 12 complete: polished frontend login panel with custom styled auth form.
- Commit 13 complete: enforce per-user recipe collections, move login form to dedicated `/login` page, and keep login page form-only (no navbar).
- Commit 14 complete: enforce login redirect gate for protected frontend routes and honor `redirect_to` on `/login`.
- Commit 15 complete: set brand name to "Recipes Library" and add branded logo in navbar/login.
- Commit 16 complete: increase login/navbar logo sizes.
- Commit 17 in progress: home/gallery list split and separate manage page UX polish.
- Commit 18 in progress: enforce private-only recipes and remove public/private UI noise.
- Commit 19 in progress: compact navbar and responsive breakpoint polish.
- Commit 20 in progress: switch branding to `rl_logo_shelf.png` and restore navbar title text.
- Commit 21 in progress: remove home subheadings and apply final sizing pass.
- Commit 22 in progress: add public recipe browsing for signed-out users and visibility badges.
- Commit 23 in progress: ignore local All-in-One migration backup artifacts.
- Commit 24 in progress: include global public recipes for signed-in users.
- Site structure now:
  - Home (`/`): searchable recipe library with Gallery/List toggle (signed-in: own recipes, signed-out: public recipes only)
  - Manage Recipes (`/manage-recipes/`): add/edit/delete UI
  - Login (`/login/`): form-only page
- Archive link removed from navigation and archive route redirects to home.
- Client can manage recipes from wp-admin and also from the frontend Manage page.

## Commands Run

- `git init`
- `gh auth status`
- `gh repo view MSulSal/recipes-hook --json url,nameWithOwner`
- `git remote add origin https://github.com/MSulSal/recipes-hook.git`
- PHP lint via LocalWP PHP executable for plugin PHP files
- `git diff --check`
- `git push`
- `git status --short`
- `rg --files app/public/wp-content/themes/recipes-hook-theme`
- `rg --files app/public/wp-content/plugins/recipe-pdf-library`

## Decisions Made

- Git repo is rooted at the LocalWP project folder, not `app/public`, so top-level docs can be tracked cleanly.
- Recipe functionality will be implemented as a portable custom plugin.
- WordPress core and generated/local files will not be committed.
- Theme owns visual presentation and layout.
- Plugin owns recipe business logic and secure CRUD handlers.
- Keep plugin portable so it can be zipped/uploaded separately if a host has a 60 MB import limit.
- Client does not need FTP or code access.

## Known Issues

- `php` CLI is not available in PATH in this environment, so PHP lint could not be run from terminal.

## Next Recommended Step

Manual WordPress QA in LocalWP browser:
1. Confirm Home shows searchable library with Gallery/List toggle and thumbnail previews.
2. Confirm `/manage-recipes/` handles add/edit/delete.
3. Confirm navbar is compact and logo-brand is correctly sized.
4. Confirm `/recipe-archive/` redirects to `/`.
5. Confirm mobile/tablet/desktop layout behavior is smooth.
6. Confirm home page no longer shows heading/subheading intro copy.
7. Confirm visibility toggle and Public/Private badges are working.
8. Confirm signed-in users can see public recipes from other accounts.

## Latest Commit Summary

- `chore: initialize WordPress project repo and documentation`
- `feat: scaffold recipe PDF library plugin`
- `feat: add PDF upload field for recipes`
- `feat: add recipe library shortcode with search and filters`
- `feat: add recipe detail PDF viewer`
- `feat: index PDF text for recipe search`
- `style: polish recipe library layout for client demo`
- `docs: finalize client handoff and proposal notes`
- `style: refine recipe library ui and detail experience`
- `feat: add custom theme and move recipe presentation out of plugin`
- `feat: add authenticated frontend recipe management ui`
- `style: polish frontend login experience`
- Pending commit: `feat: enforce per-user recipe collections and dedicated login page`
- `feat: enforce per-user recipe collections and dedicated login page`
- `feat: enforce frontend login redirects for protected routes`
- `style: add Recipes Library branding and logo`
- `style: increase Recipes Library logo sizing`
- Pending commit: `feat: reorganize recipe home and manage workflows`
- Pending commit: `feat: finalize home/manage split with private-only recipe flow`
- Pending commit: `style: compact navbar and improve responsive breakpoints`
- Pending commit: `style: apply shelf logo branding and navbar title text`
- Pending commit: `style: finalize home hero removal and responsive sizing`
- Pending commit: `feat: add public recipe browsing and visibility badges`
- Pending commit: `chore: ignore local migration backup artifacts`
- Pending commit: `fix: include public recipes in signed-in library view`
