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

- Repo and docs scaffold in progress.
- Functional plugin code has not been added yet.

## Commands Run

- `git init`
- `gh auth status`
- `gh repo view MSulSal/recipes-hook --json url,nameWithOwner`

## Decisions Made

- Git repo is rooted at the LocalWP project folder, not `app/public`, so top-level docs can be tracked cleanly.
- Recipe functionality will be implemented as a portable custom plugin.
- WordPress core and generated/local files will not be committed.

## Known Issues

- None yet.

## Next Recommended Step

Commit the repo/documentation scaffold, then create the `Recipe PDF Library` plugin and register the recipe post type and taxonomies.

## Latest Commit Summary

Pending commit: `chore: initialize WordPress project repo and documentation`

