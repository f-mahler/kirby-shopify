<?php

@include_once __DIR__ . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();
@include_once __DIR__ . '/src/KirbyShopify.php';
@include_once __DIR__ . '/src/models/shopify.products.php';
@include_once __DIR__ . '/src/models/shopify.product.php';
@include_once __DIR__ . '/src/models/shopify.collections.php';
@include_once __DIR__ . '/src/models/shopify.collection.php';

Kirby::plugin('tristanb/kirby-shopify', [
    'options' => [
      'cache.api' => true
    ],
    'fields' => [
      'button' => [
          'props' => [
              'label' => function (string $label = 'Button') {
                  return $label;
              },
              'text' => function (string $text = 'Click here') {
                  return $text;
              },
              'url' => function (string $url) {
                  return $url;
              },
              'theme' => function (string $theme = null) {
                  return $theme;
              },
              'icon' => function (string $icon = 'refresh') {
                  return $icon;
              },
              'open' => function (bool $open = false) {
                  return $open;
              },
              'reload' => function (bool $reload = false) {
                  return $reload;
              },
              'help' => function (string $help = null) {
                  return $help;
              },
          ],
          'computed' => [
              'text' => function () {
                  if( $text = $this->text ){
                      $text = $this->model()->toSafeString($text);
                      return $text;
                  }
              },
              'url' => function () {
                  if( $url = $this->url ){
                      $url = $this->model()->toSafeString($url);
                      return $url;
                  }
              }
          ],
      ]
  ],
    'collections' => [
      'kirby-shopify.productsPage' => function ($site) {
            return $site->pages()->filterBy('intendedTemplate', 'shopify.products')->first();
      },
      'kirby-shopify.products' => function ($site) {
            return collection('kirby-shopify.productsPage')->children();
      },
      'kirby-shopify.collectionsPage' => function ($site) {
            return $site->pages()->filterBy('intendedTemplate', 'shopify.collections')->first();
      },
      'kirby-shopify.collections' => function ($site) {
            return collection('kirby-shopify.collectionsPage')->children();
      }
    ],
    'pageModels' => [
        'shopify.products' => 'ShopifyProductsPage',
        'shopify.product' => 'ShopifyProductPage',
        'shopify.collections' => 'ShopifyCollectionsPage',
        'shopify.collection' => 'ShopifyCollectionPage',
    ],
    'blueprints' => [
        'pages/shopify.products' => __DIR__ . '/src/blueprints/shopify.products.yml',
        'pages/shopify.product' => __DIR__ . '/src/blueprints/shopify.product.yml',
        'pages/shopify.collections' => __DIR__ . '/src/blueprints/shopify.collections.yml',
        'pages/shopify.collection' => __DIR__ . '/src/blueprints/shopify.collection.yml'
    ],
    'snippets' => [
        'kirby-shopify.product.structured-data' => __DIR__ . '/src/snippets/product.structured-data.php'
    ],
    'fieldMethods' => [
        'img_url' => function ($field, string $size = '') {
            if (is_string($field)) {
              $src = $field;
            } elseif ($field->src()->isNotEmpty()) {
              $src = $field->src();
            }
            return preg_replace('/\.(jpg|jpeg|png|bmp|gif)/', '_'.$size.'.$1', $src);
        }
    ],
    'pageMethods' => [
        'inventory_quantity' => function () {
            $inventory = 0;
            foreach ($this->shopifyVariants()->toStructure() as $key => $variant) {
              $inventory += $variant->inventory_quantity()->int();
            }
            return $inventory;
        },
        'isAvailable' => function () {
            return true;
        },
        'hasImages' => function () {
            return $this->shopifyImages()->toStructure()->count() > 0;
        },
        'toProduct' => function () {
            return collection('kirby-shopify.products')->findBy('shopifyHandle', $this->shopifyHandle()->value());
        },
        'toProducts' => function () {
            $products = new Collection();
            foreach ($this->children() as $key => $p) {
              if ($p = $p->toProduct()) {
                $products->add($p);
              }
            }
            return $products;
        }
    ],
    'routes' => [
      [
            'pattern' => 'kirby-shopify/api/opendashboard',
            'method' => 'GET',
            'action' => function() {
                  $shopurl = $_ENV['SHOP_URL'];
                  $shopid = explode('.', $shopurl)[0];
                  $newurl = 'https://admin.shopify.com/store/' . $shopid;
                  go($newurl);
            }
      ],
      [
            'pattern' => 'kirby-shopify/api/cache/button',
            'method' => 'GET',
            'action'  => function () {
                  if(kirby()->user()) {
                        \KirbyShopify\App::clearCache();
                        \KirbyShopify\App::clearKirbyCache();
                        return Response::json(["status" => "success", "code" => 200, "message" => "Cache cleared"]);
                  }
            }
      ],
      [
        'pattern' => 'kirby-shopify/api/cache/clear',
        'method' => 'POST',
        'action'  => function () {
          $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
          $data = file_get_contents('php://input');
          $verified = \KirbyShopify\App::verifyWebhook($data, $hmac_header);
          if ($verified) {
            \KirbyShopify\App::clearCache();
            \KirbyShopify\App::clearKirbyCache();
            return Response::json(["status" => "success", "code" => 200, "message" => "Cache cleared"]);
          } else {
            return Response::json(["status" => "success", "code" => 200, "message" => "Identication failed"]);
          }
        }
      ],
      [
        'pattern' => 'kirby-shopify/api/cache/products/clear',
        'method' => 'POST',
        'action'  => function () {
          $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
          $data = file_get_contents('php://input');
          $verified = \KirbyShopify\App::verifyWebhook($data, $hmac_header);

          if ($verified) {
            \KirbyShopify\App::clearProductsCache();
            \KirbyShopify\App::clearKirbyCache();
            return Response::json(["status" => "success", "code" => 200, "message" => "Cache cleared"]);
          } else {
            return Response::json(["status" => "success", "code" => 200, "message" => "Identication failed"]);
          }
        }
      ],
      [
        'pattern' => 'kirby-shopify/api/cache/collections/clear',
        'method' => 'POST',
        'action'  => function () {
          $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
          $data = file_get_contents('php://input');
          $verified = \KirbyShopify\App::verifyWebhook($data, $hmac_header);

          if ($verified) {
            \KirbyShopify\App::clearCollectionsCache();
            \KirbyShopify\App::clearKirbyCache();
            return Response::json(["status" => "success", "code" => 200, "message" => "Cache cleared"]);
          } else {
            return Response::json(["status" => "success", "code" => 200, "message" => "Identication failed"]);
          }
        }
      ]
      ],
      'components' => [
            'file::version'       => include __DIR__ . '/src/components/previewImage.php'
      ]
]);
