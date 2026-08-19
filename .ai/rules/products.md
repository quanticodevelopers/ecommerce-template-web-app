---
paths:
  - 'app/Models/ProductImage.php,database/migrations/*product_images*,resources/js/pages/admin/products/**'
---

# Products

## Product image size paths
Each logical product image stores three WebP-ready paths: `path` for 1200x1200, `medium_path` for 750x750, and `thumbnail_path` for 350x350. Keep one shared alt text and position; listings use the thumbnail path.

## Product image size paths
Product images keep the configured primary variant in `path` and all other variant paths in the JSON `variants` map. Variant names, dimensions, primary variant, and listing variant come from `config/product-images.php`; adding a size must not require new database columns.

## Flexible variants supersede fixed paths
This rule supersedes the earlier fixed three-path rule. Do not use `medium_path` or `thumbnail_path`; use primary `path`, JSON `variants`, and `config/product-images.php`.
