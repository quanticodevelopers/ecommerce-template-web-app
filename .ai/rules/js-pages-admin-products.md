---
paths:
  - '{app/Http/Resources/Admin/ProductResource.php,app/Http/Controllers/Admin/ProductController.php,resources/js/types/admin/product*.ts,resources/js/pages/admin/products/**}'
---

# Js Pages Admin Products

## Use one canonical Product resource contract
ProductResource must provide the same complete Product entity shape to listings and detail pages. Serialize ProductFlag as { value, label } or null in this resource. Keep ProductFormResource and EditableProduct as a separate form DTO whose flag is the scalar enum value.

## Load product image relationships per page
This supersedes the requirement for identical complete image payloads in listing and detail. Keep the shared Product contract, with optional primary_image and images: index eager-loads primaryImage only; show eager-loads images only. ProductResource omits unloaded image relationships without lazy loading. Both use the same ProductImage shape, exposing all available variant URLs, including the primary size.
