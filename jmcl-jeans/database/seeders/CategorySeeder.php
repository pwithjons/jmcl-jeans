<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'Jeans' => ['Slim Fit', 'Regular Fit', 'Skinny Fit', 'Baggy Fit'],
            'Shirts' => ['Casual Shirts', 'Formal Shirts', 'Denim Shirts'],
            'T-Shirts' => ['Basic Tees', 'Graphic Tees', 'Polo Tees'],
            'Jackets' => ['Denim Jackets', 'Bomber Jackets'],
            'Denim Accessories' => ['Belts', 'Caps'],
        ];

        $sort = 0;

        foreach ($structure as $categoryName => $subcategories) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => "Premium {$categoryName} from JMCL JEANS LTD.",
                    'meta_title' => "{$categoryName} | JMCL JEANS LTD",
                    'meta_description' => "Shop premium {$categoryName} at JMCL JEANS LTD — modern fits, quality denim.",
                    'status' => true,
                    'sort_order' => $sort++,
                ]
            );

            $subSort = 0;
            foreach ($subcategories as $subName) {
                Subcategory::updateOrCreate(
                    ['slug' => Str::slug($categoryName.'-'.$subName)],
                    [
                        'category_id' => $category->id,
                        'name' => $subName,
                        'description' => "{$subName} under {$categoryName}.",
                        'status' => true,
                        'sort_order' => $subSort++,
                    ]
                );
            }
        }
    }
}
