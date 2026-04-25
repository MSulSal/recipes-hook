# Client Handoff

This guide is for the site owner. The site stores each recipe as a PDF, so you do not need to retype ingredients or instructions.

## Log Into WordPress

Go to your WordPress login URL, usually:

`https://your-domain.com/wp-admin`

Use the admin username and password provided during handoff.

All routine recipe work happens in WordPress admin. You do not need GitHub, FTP, LocalWP, or code access to manage recipes.

## Activate Theme and Plugin

1. Go to **Appearance > Themes** and activate **Recipes Hook Theme**.
2. Go to **Plugins > Installed Plugins** and activate **Recipe PDF Library**.

This setup keeps visuals in the theme and recipe behavior in the plugin.

## Login Page

Use the dedicated login page:

`/login`

The login page is a standalone form-only screen.
When logged out, protected site routes redirect to `/login`.

## Manage Recipes From the Site

After login, the homepage includes a management panel where you can:

- Add a recipe with PDF upload
- Edit title, tags, categories, and notes
- Replace or remove the current PDF
- Delete a recipe (moves to Trash)

This is the primary workflow for everyday use. wp-admin remains available for advanced settings.

Logged-out visitors see a homepage prompt that links to `/login`.
Each user account has its own recipe collection. Users only see and manage their own recipes on the site.

## Add a Recipe PDF

After the plugin is active, go to **Recipes > Add New** in WordPress. Enter a recipe title and use the **Recipe PDF** box to upload or select a PDF from the WordPress Media Library. The site will display this PDF as the recipe.

You should not need FTP, GitHub, code editing, or anything outside WordPress admin to manage recipes.

## Title, Categories, and Tags

Use a clear title such as "Spicy Pork Scissor Cut Noodles Full." Categories and tags are optional, but they help visitors browse and search.

If you leave the title blank and choose a PDF, the site will try to create a title from the PDF filename. For example, `Spicy_Pork_Scissor_Cut_Noodles_Full.pdf` becomes "Spicy Pork Scissor Cut Noodles Full."

## View Recipes on the Site

The recipe library page will use this shortcode:

`[recipe_pdf_library]`

Create a normal WordPress page titled **Recipes**, add the shortcode to the page content, and publish it. Visitors will see a search box, category filter, and recipe cards.

Clicking **View Recipe** opens a recipe detail page with the PDF embedded in the browser when supported. Visitors can also open the PDF in a new tab or download it.

The library page shows active search/category filters and a result count so it is easy to confirm what is being filtered.

## Search

Search matches recipe titles, PDF filenames, categories, and tags. The site also tries to index text inside machine-readable PDFs, so visitors may be able to search for words that appear inside the PDF itself.

## Scanned PDFs

If a PDF is a scanned image, WordPress may not be able to search the text inside it. In that case, use a descriptive title, category, and tags. The recipe will still display and download normally.

## Replace or Delete a PDF

Edit the recipe in WordPress and choose a different PDF in the **Recipe PDF** box. To remove the PDF but keep the recipe draft, click **Remove PDF** and update the recipe. To remove a recipe entirely, move the recipe to Trash.

If a recipe detail page says the PDF is unavailable, edit the recipe in WordPress and select the PDF again from the **Recipe PDF** box.

## Add the Recipe Page to Navigation

Go to **Appearance > Editor** or **Appearance > Menus**, depending on the theme, and add the page titled "Recipes" to the site navigation.

## Plugin Updates or Moving Hosts

If the site is moved to another host, the `Recipe PDF Library` plugin can be uploaded separately as a zip through **Plugins > Add New > Upload Plugin**. Recipe PDFs are media library files, so make sure the migration includes uploads or re-upload the PDFs from WordPress admin.
