---
paths:
  - 'app/Models/{Customer,Administrator}.php,app/Http/Controllers/{Store,Admin}/**,tests/Feature/{Store,Admin}/**'
---

# Store Admin

## Customers and administrators are separate identities
Customer and Administrator use separate tables, guards, providers, and password brokers. Administrator accounts must never be included in customer queries or authenticated through the store guard, and Customer accounts must never authenticate through the admin guard. This explicitly supersedes the former shared User-role design.
