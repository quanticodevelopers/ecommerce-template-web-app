---
paths:
  - 'resources/js/pages/admin/products/components/*rich-text-editor*.tsx'
---

# Components

## Subscribe TipTap toolbars with useEditorState
TipTap editor state is not automatically reactive in React. Toolbar values derived from `isActive()`, heading attributes, or command availability must come from `useEditorState`; reading them directly during render leaves the toolbar stuck on its initial selection.

## Initialize TipTap before subscribing
With SSR-safe `immediatelyRender: false`, `useEditor()` is initially null. Mount a child that calls `useEditorState` only after the editor exists; gating the parent on a `useEditorState` result created from null can deadlock on the loading state. Synchronize external HTML with `setContent(..., { emitUpdate: false })` only when it differs.

## Render a stable TipTap SSR first paint
Admin product pages use Inertia SSR, so keep `immediatelyRender: false`. While the editor instance initializes, render the complete stable editor surface and existing sanitized HTML—not an animated empty skeleton—then swap to the live editor. Keep extensions stable and set `shouldRerenderOnTransaction: false`; toolbar reactivity belongs in `useEditorState`.

## Key nullable TipTap toolbar across initialization
This supersedes the earlier requirement to hide the toolbar until the editor exists. Render the toolbar during SSR with a null editor and safe defaults, then change its React `key` when the editor becomes ready so `useEditorState` remounts against the live instance. This preserves first paint without the null-subscription deadlock.
