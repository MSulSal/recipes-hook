# Deployment and Migration

## Move From LocalWP to Hosting

Use a WordPress migration plugin or the host's migration tool to move the full site, including the database and media library, from LocalWP to production hosting.

## Plugin Zip/Export Option

The custom plugin can also be zipped from:

`app/public/wp-content/plugins/recipe-pdf-library/`

Install it on another WordPress site through **Plugins > Add New > Upload Plugin**.

## Migration Plugin Option

A simple migration plugin such as Duplicator, All-in-One WP Migration, or the hosting provider's migration tool can move the complete site. Confirm plugin licensing and size limits before relying on a specific tool.

## Domain and Hosting Handoff

The client should own the domain, hosting account, WordPress admin account, and any GitHub repo access they want to keep.

## Files Not Committed

Do not commit WordPress core, `wp-config.php`, `.htaccess`, database dumps, uploads, cache folders, LocalWP machine files, secrets, third-party themes, or third-party plugins.

## Client Ownership Checklist

- Domain registrar access
- Hosting account access
- WordPress administrator account
- Backup/migration access
- GitHub repo access, if desired

