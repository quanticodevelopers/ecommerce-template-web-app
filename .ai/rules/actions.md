---
paths:
  - 'app/Http/Controllers/Admin/**,app/Actions/**'
---

# Actions

## Delegate admin writes to module Actions
Keep admin controllers focused on HTTP concerns. Delegate create, update, and other state-changing module operations to Actions grouped by domain, following the existing Products pattern; keep read-only page queries in controllers unless they form a reusable business operation.
