---
paths:
  - 'app/Actions/Products/**,app/Http/Requests/Admin/*ProductRequest.php,resources/js/pages/admin/products/**'
---

# Pages Admin Products

## Represent product image edits as ordered slots
Product create/update forms submit `images` as the final ordered list of slots. Each create slot contains `file`; each update slot contains exactly one existing `id` or new `file`. The array index is persisted as `position`, with 1–5 total images. New variant files use a unique filename so inserting/reordering cannot overwrite existing files.
