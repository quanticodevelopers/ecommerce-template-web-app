import { ArrowLeft01Icon, ArrowRight01Icon, Image01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { useId, useState } from 'react'
import type { Swiper as SwiperInstance } from 'swiper'
import { A11y, Keyboard, Navigation, Thumbs } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/react'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { cn } from '@/lib/utils'
import type { ProductDetailImage } from '@/types'

type ProductImageGalleryProps = {
  images: ProductDetailImage[]
  productName: string
}

export default function ProductImageGallery({ images, productName }: ProductImageGalleryProps) {
  const navigationId = useId().replaceAll(':', '')
  const previousButtonClass = `product-gallery-previous-${navigationId}`
  const nextButtonClass = `product-gallery-next-${navigationId}`
  const [activeIndex, setActiveIndex] = useState(0)
  const [mainSwiper, setMainSwiper] = useState<SwiperInstance | null>(null)
  const [thumbnailSwiper, setThumbnailSwiper] = useState<SwiperInstance | null>(null)
  const hasMultipleImages = images.length > 1

  const selectImage = (index: number): void => {
    mainSwiper?.slideTo(index)
    setActiveIndex(index)
  }

  if (images.length === 0) {
    return (
      <Card className="p-4 shadow-none lg:p-5">
        <div className="mx-auto flex aspect-square w-full max-w-2xl flex-col items-center justify-center gap-3 rounded-xl bg-muted/50 text-muted-foreground ring-1 ring-foreground/10">
          <HugeiconsIcon
            icon={Image01Icon}
            className="size-10"
            strokeWidth={1.5}
          />
          <span className="text-sm">Este producto no tiene imágenes</span>
        </div>
      </Card>
    )
  }

  return (
    <Card className="gap-4 p-4 shadow-none lg:p-5">
      <div className="mx-auto flex w-full max-w-2xl flex-col gap-3">
        <div className="relative aspect-square w-full overflow-hidden rounded-xl bg-muted/50 ring-1 ring-foreground/10">
          {hasMultipleImages && (
            <>
              <Button
                type="button"
                variant="secondary"
                size="icon"
                className={cn(
                  previousButtonClass,
                  'absolute top-1/2 left-3 z-10 -translate-y-1/2 rounded-full bg-background/90 shadow-md backdrop-blur-sm [&.swiper-button-disabled]:pointer-events-none [&.swiper-button-disabled]:opacity-30',
                )}
                aria-label="Ver imagen anterior"
              >
                <HugeiconsIcon
                  icon={ArrowLeft01Icon}
                  strokeWidth={1.5}
                />
              </Button>
              <Button
                type="button"
                variant="secondary"
                size="icon"
                className={cn(
                  nextButtonClass,
                  'absolute top-1/2 right-3 z-10 -translate-y-1/2 rounded-full bg-background/90 shadow-md backdrop-blur-sm [&.swiper-button-disabled]:pointer-events-none [&.swiper-button-disabled]:opacity-30',
                )}
                aria-label="Ver imagen siguiente"
              >
                <HugeiconsIcon
                  icon={ArrowRight01Icon}
                  strokeWidth={1.5}
                />
              </Button>
            </>
          )}

          <Swiper
            className="size-full"
            modules={[A11y, Keyboard, Navigation, Thumbs]}
            navigation={
              hasMultipleImages
                ? {
                    prevEl: `.${previousButtonClass}`,
                    nextEl: `.${nextButtonClass}`,
                  }
                : false
            }
            keyboard={{ enabled: hasMultipleImages, onlyInViewport: true }}
            thumbs={{ swiper: thumbnailSwiper?.destroyed === false ? thumbnailSwiper : null }}
            grabCursor={hasMultipleImages}
            onSwiper={setMainSwiper}
            onSlideChange={(swiper) => setActiveIndex(swiper.realIndex)}
            a11y={{
              prevSlideMessage: 'Imagen anterior',
              nextSlideMessage: 'Imagen siguiente',
              firstSlideMessage: 'Esta es la primera imagen',
              lastSlideMessage: 'Esta es la última imagen',
              paginationBulletMessage: 'Ir a la imagen {{index}}',
            }}
          >
            {images.map((image) => (
              <SwiperSlide key={image.id}>
                <div className="flex size-full items-center justify-center">
                  <img
                    src={image.url}
                    alt={image.alt || productName}
                    className="size-full object-contain"
                  />
                </div>
              </SwiperSlide>
            ))}
          </Swiper>

          {hasMultipleImages && (
            <span className="absolute right-3 bottom-3 z-10 rounded-full bg-background/90 px-2.5 py-1 text-xs font-medium shadow-sm backdrop-blur-sm">
              {activeIndex + 1} / {images.length}
            </span>
          )}
        </div>

        {hasMultipleImages && (
          <div className="min-w-0 overflow-hidden">
            <Swiper
              className="w-full"
              modules={[Thumbs]}
              slidesPerView={5}
              spaceBetween={12}
              watchSlidesProgress
              onSwiper={setThumbnailSwiper}
            >
              {images.map((image, index) => (
                <SwiperSlide
                  key={image.id}
                  className="aspect-square !h-auto"
                >
                  <button
                    type="button"
                    className={cn(
                      'box-border block size-full overflow-hidden rounded-lg border-2 bg-muted/50 transition focus-visible:border-ring focus-visible:outline-none',
                      activeIndex === index ? 'border-primary opacity-100' : 'border-foreground/10 opacity-60 hover:border-foreground/30 hover:opacity-100',
                    )}
                    aria-label={`Ver imagen ${index + 1} de ${productName}`}
                    aria-pressed={activeIndex === index}
                    onClick={() => selectImage(index)}
                  >
                    <img
                      src={image.thumbnail_url}
                      alt=""
                      className="size-full object-cover"
                    />
                  </button>
                </SwiperSlide>
              ))}
            </Swiper>
          </div>
        )}
      </div>
    </Card>
  )
}
