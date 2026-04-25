# QA Checklist

Record results here as features are built.

- Add recipe PDF: Ready for manual test in wp-admin
- Add recipe PDF: Ready for manual test in frontend and wp-admin
- Edit recipe title: Pending
- Edit recipe title: Ready for manual test in frontend
- Add category/tag: Pending
- Search by title: Ready for manual test on shortcode page
- Search by filename: Ready for manual test on shortcode page
- Search by category/tag: Ready for manual test on shortcode page
- Search by PDF text if implemented: Ready for manual test with a machine-readable PDF
- View PDF detail page: Ready for manual test
- Download PDF: Ready for manual test
- Download PDF: Ready for manual test
- Mobile layout: Ready for manual test
- Empty state: Ready for manual test on shortcode page
- No-results state: Ready for manual test on shortcode page
- Bad upload / non-PDF protection: Ready for manual test in wp-admin
- Client handoff check: Pending
- Client handoff check: Ready for final pass
- Home library gallery view: Ready for manual test
- Home library list view: Ready for manual test
- Recipe thumbnail preview from PDF: Ready for manual test
- Manage Recipes page add/edit/delete flow: Ready for manual test
- Archive route redirect to home: Ready for manual test
- Larger logo on navbar/login page: Ready for manual test
- Compact navbar height: Ready for manual test
- Tablet layout (741px-1024px): Ready for manual test
- Desktop layout (1025px+): Ready for manual test
- Home intro headings removed: Ready for manual test

## Manual QA Notes

- Commit 1: Repo/docs scaffold only. No WordPress behavior to test yet.
- Commit 2: Activate `Recipe PDF Library` in **Plugins** and confirm **Recipes**, **Recipe Categories**, and **Recipe Tags** appear in WordPress admin.
- Commit 3: In **Recipes > Add New**, upload/select a PDF, save, confirm the PDF column says **View PDF**, and confirm a non-PDF file is rejected.
- Commit 4: Create a **Recipes** page with `[recipe_pdf_library]`, add at least one recipe, and test search by title, PDF filename, category, and tag.
- Commit 5: Click **View Recipe**, confirm the PDF embeds, **Open PDF** opens the file, **Download PDF** downloads it, and a recipe with no PDF shows the unavailable message.
- Commit 6: Upload a machine-readable PDF, save the recipe, then search for a distinctive word from inside the PDF. Also confirm a scanned PDF still works by title/filename/category/tag if text is not indexed.
- Commit 7: Check the recipe library and detail page around mobile width. Confirm the search form stacks, buttons fit, cards do not overflow, and the PDF viewer remains usable.
- Commit 8: Documentation finalized. Code syntax checks passed with LocalWP PHP, but browser/wp-admin QA still needs to be performed in the running LocalWP site.
- Commit 9: Confirm UI polish: library header text displays, active filter pills appear, recipe card metadata/actions are visible, and detail header/action layout is clean on desktop and mobile.
- Commit 17: Confirm home page library has working search/category filters plus Gallery/List toggle; confirm PDF preview image shows when available; confirm `/manage-recipes/` owns add/edit/delete UI; confirm `/recipe-archive/` redirects to home.
- Commit 19: Confirm navbar height is compact with logo-only brand, and verify seamless behavior across mobile/tablet/desktop breakpoints (search controls, cards, actions, and manage page layout).

## Final Manual Demo Flow

1. Activate **Recipes Hook Theme** in **Appearance > Themes**.
2. Activate **Recipe PDF Library** in **Plugins**.
3. Log in on the homepage and add a recipe with `Spicy_Pork_Scissor_Cut_Noodles_Full.pdf`.
4. Edit the same recipe from `/manage-recipes/`.
5. Delete a test recipe from `/manage-recipes/`.
6. Confirm title/category/tag/search/detail/download workflows.
7. Confirm logged-out homepage shows the custom login panel and that login redirects back to homepage.
8. Confirm login form appears only on `/login` and not on homepage.
9. Confirm User A cannot view or edit User B recipes.
10. Confirm `/login` has only the login form (no site navbar/header).
