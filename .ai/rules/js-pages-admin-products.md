---
paths:
  - '{app/Http/Resources/Admin/ProductResource.php,app/Http/Controllers/Admin/ProductController.php,resources/js/types/admin/product*.ts,resources/js/pages/admin/products/**}'
---

# Js Pages Admin Products

## Use one canonical Product resource contract
ProductResource must provide the same complete Product entity shape to listings and detail pages. Serialize ProductFlag as { value, label } or null in this resource. Keep ProductFormResource and EditableProduct as a separate form DTO whose flag is the scalar enum value.
