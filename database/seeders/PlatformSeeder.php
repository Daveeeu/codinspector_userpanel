<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'Shopify',
                'description' => 'A leading e-commerce platform for online stores and retail point-of-sale systems.'
            ],
            [
                'name' => 'WooCommerce',
                'description' => 'A customizable, open-source eCommerce platform built on WordPress.'
            ],
            [
                'name' => 'Magento',
                'description' => 'An open-source e-commerce platform written in PHP.'
            ],
            [
                'name' => 'BigCommerce',
                'description' => 'A leading cloud eCommerce platform for fast-growing businesses.'
            ],
            [
                'name' => 'Wix eCommerce',
                'description' => 'An easy-to-use website builder with eCommerce capabilities.'
            ],
            [
                'name' => 'Squarespace Commerce',
                'description' => 'A platform to build websites and sell products online.'
            ],
            [
                'name' => 'PrestaShop',
                'description' => 'An open-source e-commerce solution for building online stores.'
            ],
            [
                'name' => 'OpenCart',
                'description' => 'An open-source PHP-based online e-commerce solution.'
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::create($platform);
        }
    }
}
