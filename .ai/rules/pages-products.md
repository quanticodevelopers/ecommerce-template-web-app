---
paths:
  - 'resources/js/pages/**/products/**'
---

# Pages Products

## Use Swiper for product galleries
Build product image galleries with the installed Swiper React package so Admin and Store share the same interaction model (touch, keyboard, navigation, and thumbnails). In Admin product row actions, keep the external-link action reserved for the future storefront URL and use a separate eye action for the internal product detail.

## Reserve five product gallery thumbnail slots
Product galleries support at most five images. Render the thumbnail Swiper with five equal-width slots so fewer images stay left-aligned and the unused slots remain blank; thumbnail clicks and active styling must be driven by the main Swiper's active index.
