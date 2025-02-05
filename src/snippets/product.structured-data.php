<?php $productImages = $page->shopifyImages()->toStructure() ?>

<script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "<?= $page->shopifyTitle()->escape() ?>",
  "url": "<?= $page->url() ?>",
  <?php if ($productImages->count()): ?>
    "image": [
      "<?= $productImages->first()->src() ?>"
    ],
  <?php endif ?>
  "description": "<?= preg_replace('/\r|\n/','\n',trim(Escape::html(Str::unhtml($page->shopifyDescriptionHTML())))) ?>",
  "brand": {
    "@type": "Brand",
    "name": "<?= $page->shopifyVendor()->escape() ?>"
  },
  <?php if ($page->shopifyVariants()->toStructure()->count()): ?>
    "offers": [
      <?php $index = 1; foreach ($page->shopifyVariants()->toStructure() as $key => $variant): ?>{
          "@type" : "Offer",
          "name" : "<?= $variant->title()->escape() ?>",
          "availability" : "http://schema.org/<?= r($page->isAvailable(), 'InStock', 'OutOfStock') ?>",
          "itemCondition" : "http://schema.org/NewCondition",
          "price" : "<?= $variant->price() ?>",
          "priceCurrency" : "CHF",
          "url" : "<?= $page->url() ?>?variant=<?= $variant->id() ?>"
        }<?= r($page->shopifyVariants()->toStructure()->count() != $index, ',') ?>
      <?php $index++; endforeach ?>
    ]
  <?php endif ?>
}
</script>
