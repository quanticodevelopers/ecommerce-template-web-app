---
paths:
  - 'resources/js/{types,hooks,lib,components,layouts,pages}/**'
---

# Typeshookslibcomponentslayoutspages

## Separate environment-owned frontend modules
Place Admin-only types, hooks, and utilities under their area's admin/ directory, and Store-only modules under store/. Keep only genuinely cross-environment primitives at the area root. Admin code must not import Store-owned modules and Store code must not import Admin-owned modules; use environment-specific authenticated identity hooks instead of a shared User hook.
