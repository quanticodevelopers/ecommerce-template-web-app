---
paths:
  - 'resources/js/{components,layouts,pages}/**'
---

# Componentslayoutspages

## Keep Store and Admin design systems isolated
Frontend visual components belong under components/admin/** or components/store/**. Admin pages/layouts/components must never import Store components, and Store code must never import Admin components. Do not recreate a global components/ui tree; duplicate shadcn source manually inside each environment.
