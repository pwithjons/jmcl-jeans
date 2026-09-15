<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Demo products only. Images use https://placehold.co — a free,
     * no-auth placeholder image service — so the storefront looks
     * populated without bundling any copyrighted product photography.
     * Replace with real product photos before going live.
     */
    private array $colorSwatches = [
        'Black' => '#1a1a1a',
        'Blue' => '#3a6cab',
        'Navy' => '#1a2e4a',
        'Grey' => '#6d6d6d',
        'White' => '#f6f6f6',
    ];

    public function run(): void
    {
        $products = [
            [
                'category' => 'Jeans', 'subcategory' => 'Slim Fit',
                'name' => 'JMCL Classic Slim Fit Jeans',
                'price' => 2450, 'discount_price' => 1990,
                'colors' => ['Black', 'Blue', 'Navy'],
                'featured' => true,
            ],
            [
                'category' => 'Jeans', 'subcategory' => 'Skinny Fit',
                'name' => 'JMCL Stretch Skinny Jeans',
                'price' => 2650, 'discount_price' => null,
                'colors' => ['Black', 'Grey'],
                'featured' => true,
            ],
            [
                'category' => 'Jeans', 'subcategory' => 'Baggy Fit',
                'name' => 'JMCL Relaxed Baggy Jeans',
                'price' => 2800, 'discount_price' => 2390,
                'colors' => ['Blue', 'Navy'],
                'featured' => false,
            ],
            [
                'category' => 'Shirts', 'subcategory' => 'Denim Shirts',
                'name' => 'JMCL Heritage Denim Shirt',
                'price' => 1850, 'discount_price' => null,
                'colors' => ['Blue', 'Navy', 'Grey'],
                'featured' => true,
            ],
            [
                'category' => 'Shirts', 'subcategory' => 'Casual Shirts',
                'name' => 'JMCL Everyday Casual Shirt',
                'price' => 1450, 'discount_price' => 1190,
                'colors' => ['White', 'Grey', 'Black'],
                'featured' => false,
            ],
            [
                'category' => 'T-Shirts', 'subcategory' => 'Basic Tees',
                'name' => 'JMCL Essential Basic Tee',
                'price' => 650, 'discount_price' => null,
                'colors' => ['Black', 'White', 'Grey'],
                'featured' => true,
            ],
            [
                'category' => 'T-Shirts', 'subcategory' => 'Graphic Tees',
                'name' => 'JMCL Denim Culture Graphic Tee',
                'price' => 850, 'discount_price' => 690,
                'colors' => ['Black', 'White'],
                'featured' => false,
            ],
            [
                'category' => 'Jackets', 'subcategory' => 'Denim Jackets',
                'name' => 'JMCL Signature Denim Jacket',
                'price' => 3450, 'discount_price' => 2990,
                'colors' => ['Blue', 'Black'],
                'featured' => true,
            ],
            [
                'category' => 'Jackets', 'subcategory' => 'Bomber Jackets',
                'name' => 'JMCL Urban Bomber Jacket',
                'price' => 3800, 'discount_price' => null,
                'colors' => ['Black', 'Navy'],
                'featured' => false,
            ],
            [
                'category' => 'Denim Accessories', 'subcategory' => 'Belts',
                'name' => 'JMCL Genuine Leather Belt',
                'price' => 950, 'discount_price' => null,
                'colors' => ['Black'],
                'featured' => false,
            ],
        ];

        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        foreach ($products as $index => $data) {
            $category = Category::where('name', $data['category'])->first();
            $subcategory = Subcategory::where('category_id', $category->id)
                ->where('name', $data['subcategory'])
                ->first();

            $slug = Str::slug($data['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory?->id,
                    'name' => $data['name'],
                    'short_description' => "Premium quality {$data['name']} crafted for everyday comfort and style.",
                    'description' => "The {$data['name']} is part of the JMCL JEANS LTD collection, designed with premium denim fabric, reinforced stitching, and a modern fit. Available in multiple sizes and colors.",
                    'sku' => 'JMCL-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'],
                    'status' => 'published',
                    'is_featured' => $data['featured'],
                    'meta_title' => "{$data['name']} | JMCL JEANS LTD",
                    'meta_description' => "Buy {$data['name']} online at JMCL JEANS LTD. Premium denim, fast delivery, cash on delivery available.",
                ]
            );

            // Variations: every (size × color) combination for this product
            foreach ($data['colors'] as $color) {
                foreach ($sizes as $size) {
                    ProductVariation::updateOrCreate(
                        ['product_id' => $product->id, 'size' => $size, 'color' => $color],
                        [
                            'color_hex' => $this->colorSwatches[$color] ?? '#000000',
                            'sku' => strtoupper($product->sku.'-'.substr($color, 0, 3).'-'.$size),
                            'stock_quantity' => rand(5, 40),
                            'status' => true,
                        ]
                    );
                }
            }

            // Placeholder images (2 per product) — swap for real photography later
            $seedText = urlencode($data['name']);
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 0],
                [
                    'image_path' => "https://placehold.co/800x1000/1a2e4a/ffffff?text={$seedText}",
                    'alt_text' => $data['name'].' - front view',
                    'is_primary' => true,
                ]
            );
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 1],
                [
                    'image_path' => "https://placehold.co/800x1000/454545/ffffff?text={$seedText}+2",
                    'alt_text' => $data['name'].' - detail view',
                    'is_primary' => false,
                ]
            );
        }
    }
}
