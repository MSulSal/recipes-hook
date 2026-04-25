# Client Handoff

This guide is for the site owner. The site stores each recipe as a PDF, so you do not need to retype ingredients or instructions.

## Log Into WordPress

Go to your WordPress login URL, usually:

`https://your-domain.com/wp-admin`

Use the admin username and password provided during handoff.

## Add a Recipe PDF

After the plugin is active, go to **Recipes > Add New** in WordPress. Enter a recipe title and use the **Recipe PDF** box to upload or select a PDF from the WordPress Media Library. The site will display this PDF as the recipe.

You should not need FTP, GitHub, code editing, or anything outside WordPress admin to manage recipes.

## Title, Categories, and Tags

Use a clear title such as "Spicy Pork Scissor Cut Noodles Full." Categories and tags are optional, but they help visitors browse and search.

If you leave the title blank and choose a PDF, the site will try to create a title from the PDF filename. For example, `Spicy_Pork_Scissor_Cut_Noodles_Full.pdf` becomes "Spicy Pork Scissor Cut Noodles Full."

## View Recipes on the Site

The recipe library page will use this shortcode:

`[recipe_pdf_library]`

## Search

Search is planned to match recipe titles, PDF filenames, categories, and tags. If PDF text indexing is added, machine-readable PDF text may also be searchable.

## Scanned PDFs

If a PDF is a scanned image, WordPress may not be able to search the text inside it. In that case, use a descriptive title, category, and tags.

## Replace or Delete a PDF

Edit the recipe in WordPress and choose a different PDF. To remove a recipe entirely, move the recipe to Trash.

## Add the Recipe Page to Navigation

Go to **Appearance > Editor** or **Appearance > Menus**, depending on the theme, and add the page titled "Recipes" to the site navigation.
