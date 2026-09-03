---
paths:
  - 'resources/js/types/**'
  - 'resources/js/types/**/*.ts'
---

# Types

## Name entity types directly
Name canonical frontend entity types after the entity itself, such as Administrator, Brand, Category, and Product. Do not use ListItem suffixes for these types or filenames. Keep suffixes only when a contract is meaningfully different, such as ProductDetail, EditableProduct, or PaginatedProducts.

## Product has no detail type variant
Product is the single complete frontend contract for the system entity. Do not introduce ProductDetail or ProductDetailImage; use Product and ProductImage. This product-specific decision supersedes ProductDetail as an example of an allowed suffixed contract.

## Keep system entities in the shared entities directory
Place canonical system entity contracts in resources/js/types/entities and import them through @/types/entities from both environments. Keep transport and UI contracts such as forms, pagination, navigation, and administrator credentials under their owning environment. Do not duplicate Customer, Product, Brand, Category, or Administrator under admin or store.
