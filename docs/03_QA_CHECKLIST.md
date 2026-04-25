# QA Checklist

Record results here as features are built.

- Add recipe PDF: Ready for manual test in wp-admin
- Edit recipe title: Pending
- Add category/tag: Pending
- Search by title: Ready for manual test on shortcode page
- Search by filename: Ready for manual test on shortcode page
- Search by category/tag: Ready for manual test on shortcode page
- Search by PDF text if implemented: Ready for manual test with a machine-readable PDF
- View PDF detail page: Ready for manual test
- Download PDF: Ready for manual test
- Mobile layout: Ready for manual test
- Empty state: Ready for manual test on shortcode page
- No-results state: Ready for manual test on shortcode page
- Bad upload / non-PDF protection: Ready for manual test in wp-admin
- Client handoff check: Pending

## Manual QA Notes

- Commit 1: Repo/docs scaffold only. No WordPress behavior to test yet.
- Commit 2: Activate `Recipe PDF Library` in **Plugins** and confirm **Recipes**, **Recipe Categories**, and **Recipe Tags** appear in WordPress admin.
- Commit 3: In **Recipes > Add New**, upload/select a PDF, save, confirm the PDF column says **View PDF**, and confirm a non-PDF file is rejected.
- Commit 4: Create a **Recipes** page with `[recipe_pdf_library]`, publish at least one recipe, and test search by title, PDF filename, category, and tag.
- Commit 5: Click **View Recipe**, confirm the PDF embeds, **Open PDF** opens the file, **Download PDF** downloads it, and a recipe with no PDF shows the unavailable message.
- Commit 6: Upload a machine-readable PDF, publish the recipe, then search for a distinctive word from inside the PDF. Also confirm a scanned PDF still works by title/filename/category/tag if text is not indexed.
- Commit 7: Check the recipe library and detail page around mobile width. Confirm the search form stacks, buttons fit, cards do not overflow, and the PDF viewer remains usable.
- Commit 8: Documentation finalized. Code syntax checks passed with LocalWP PHP, but browser/wp-admin QA still needs to be performed in the running LocalWP site.

## Final Manual Demo Flow

1. Activate **Recipe PDF Library** in **Plugins**.
2. Create a page titled **Recipes** and add `[recipe_pdf_library]`.
3. Add the **Recipes** page to navigation.
4. Go to **Recipes > Add New** and upload `Spicy_Pork_Scissor_Cut_Noodles_Full.pdf`.
5. Publish the recipe and confirm the title/category/tag/search/detail/download workflows.
