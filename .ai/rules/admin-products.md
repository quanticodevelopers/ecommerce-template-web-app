---
paths:
  - 'app/Actions/Products/**, config/product-images.php, resources/js/pages/admin/products/**'
---

# Admin Products

## Variantes configurables de imágenes de producto
Procesa las imágenes de producto con recorte centrado 1:1 y salida AVIF en el disco public. El tamaño principal y todas las variantes se definen exclusivamente en config/product-images.php; ProductImage.path guarda la principal y variants guarda el resto, sin columnas por tamaño. El orden enviado por el formulario se persiste en position.
