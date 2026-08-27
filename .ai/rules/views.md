---
paths:
  - 'resources/css/**,resources/views/app.blade.php,vite.config.ts'
---

# Views

## Load one environment stylesheet per document
Admin and Store use independent Tailwind entry stylesheets. The Inertia root Blade view selects admin.css for admin/* components and store.css otherwise. Cross-environment navigation must perform a full document visit so the browser replaces the active stylesheet.
