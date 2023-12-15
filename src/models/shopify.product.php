<?php

class ShopifyProductPage extends Page
{
      public function previewImage() {
            // create virtual file
            $file = new File([
                  'filename' => "preview.png",
                  'parent' => $this,
                  'template' => "virtual-file",
                  'url' => $this->shopifyFeaturedImage()->toStructure()->first()->src()->value()
            ]);
            return $file;
      }
}