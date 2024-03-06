<?php

use Kirby\Data\Data;
use Kirby\Data\Yaml;
use Kirby\Cms\Page;

class ShopifyProductsPage extends Page {
      static $subpages = null;

      public function subpages()
      {
            if (static::$subpages) {
                  return static::$subpages;
            }

            return static::$subpages = Pages::factory($this->inventory()['children'], $this);
      }

      public function children(): Pages {

            if ($this->children instanceof Pages) {
                return $this->children;
            }

            $products = \KirbyShopify\App::getProducts();
            $activeproducts = array_filter($products, function($product) {
                  return $product['status'] == 'active';
            });
            $activeproductscount = count($activeproducts);

            $pages = array_map(function($product) use($activeproductscount) {
                // add one to $count
                $slug = Str::slug($product['handle']);
                $page = $this->subpages()->find($slug);
                $pagecontent = $page ? $page->content()->toArray() : null;
                  
                $shopifyProduct = [
                    'title'                       => $product['title'],
                    'shopifyStatus'               => $product['status'],
                    'shopifyTitle'                => $product['title'],
                    'shopifyID'                   => $product['id'],
                    'shopifyCreatedAt'            => $product['created_at'],
                    'shopifyUpdatedAt'            => $product['updated_at'],
                    'shopifyPublishedAt'          => $product['published_at'],
                    'shopifyHandle'               => $product['handle'],
                    'shopifyVendor'               => $product['vendor'],
                    'shopifyFeaturedImage'        => count($product['images']) > 0 ? \Kirby\Data\Yaml::encode([$product['images'][0]]) : '',
                    'shopifyImages'               => \Kirby\Data\Yaml::encode($product['images']),
                    'shopifyDescriptionHTML'      => $product['body_html'],
                    'shopifyPrice'                => count($product['variants']) > 0 ? $product['variants'][0]['price'] : '',
                    'shopifyCompareAtPrice'       => count($product['variants']) > 0 ? $product['variants'][0]['compare_at_price'] : '',
                    'shopifyType'                 => $product['product_type'],
                    'shopifyTags'                 => $product['tags'],
                    'shopifyVariants'             => \Kirby\Data\Yaml::encode($product['variants']),
                    'shopifyOptions'              => \Kirby\Data\Yaml::encode($product['options']),
                ];
                  
                if ($pagecontent) {
                    foreach ($shopifyProduct as $k => $value) {
                        unset($pagecontent[strtolower($k)]);
                    }
                }

                if($page && $page->num()) {
                    $num = $page->num();
                } else {
                    $num = $activeproductscount + 1;
                }
        
                return [
                    'slug'      => $slug,
                    'num'       => $num,
                    'template'  => 'shopify.product',
                    'model'     => 'shopify.product',
                    'files'     => $page ? $page->files()->toArray() : null,
                    'content'   => $page ? $shopifyProduct + $pagecontent : $shopifyProduct,
                ];
            
            }, $products);

            usort($pages, function($a, $b) {
                  return $a['num'] <=> $b['num'];
            });
      
            return Pages::factory($pages, $this);
    }
}
