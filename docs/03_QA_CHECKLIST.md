# QA Checklist

Record results here as features are built.

- Add recipe PDF: Ready for manual test in wp-admin
- Edit recipe title: Pending
- Add category/tag: Pending
- Search by title: Pending
- Search by filename: Pending
- Search by category/tag: Pending
- Search by PDF text if implemented: Pending
- View PDF detail page: Pending
- Download PDF: Pending
- Mobile layout: Pending
- Empty state: Pending
- No-results state: Pending
- Bad upload / non-PDF protection: Ready for manual test in wp-admin
- Client handoff check: Pending

## Manual QA Notes

- Commit 1: Repo/docs scaffold only. No WordPress behavior to test yet.
- Commit 2: Activate `Recipe PDF Library` in **Plugins** and confirm **Recipes**, **Recipe Categories**, and **Recipe Tags** appear in WordPress admin.
- Commit 3: In **Recipes > Add New**, upload/select a PDF, save, confirm the PDF column says **View PDF**, and confirm a non-PDF file is rejected.
