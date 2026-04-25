# QA Checklist

Record results here as features are built.

- Add recipe PDF: Ready for manual test in wp-admin
- Edit recipe title: Pending
- Add category/tag: Pending
- Search by title: Ready for manual test on shortcode page
- Search by filename: Ready for manual test on shortcode page
- Search by category/tag: Ready for manual test on shortcode page
- Search by PDF text if implemented: Pending
- View PDF detail page: Ready for manual test
- Download PDF: Ready for manual test
- Mobile layout: Pending
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
