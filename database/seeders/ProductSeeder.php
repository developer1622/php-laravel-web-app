<?php

/**
 * =============================================================================
 * LARAVEL DATABASE SEEDER — Populate Database with Sample Data
 * =============================================================================
 *
 * WHAT IS A SEEDER?
 * -----------------
 * A seeder is a class that inserts sample/test data into your database.
 * You run it with: php artisan db:seed
 *
 * GO EQUIVALENT:
 * In Go, you'd typically have a seed.go file or a CLI command:
 *
 *   // cmd/seed/main.go
 *   package main
 *
 *   func main() {
 *       db := connectDB()
 *
 *       products := []models.Product{
 *           {Name: "MacBook Pro", Price: 2499.99, Category: "electronics"},
 *           {Name: "iPhone 15 Pro", Price: 1199.99, Category: "electronics"},
 *       }
 *
 *       for _, p := range products {
 *           if err := db.Create(&p).Error; err != nil {
 *               log.Fatalf("failed to seed: %v", err)
 *           }
 *       }
 *       fmt.Println("Database seeded successfully!")
 *   }
 *
 * Then run: go run cmd/seed/main.go
 *
 * =============================================================================
 */

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method is called when you run: php artisan db:seed --class=ProductSeeder
     *
     * GO EQUIVALENT:
     *   func (s *ProductSeeder) Run(db *gorm.DB) error { ... }
     */
    public function run(): void
    {
        // Define sample products as an array of associative arrays.
        // This is like a Go slice of structs:
        //
        // GO EQUIVALENT:
        //   products := []models.Product{
        //       {
        //           Name:        "MacBook Pro 16-inch",
        //           Description: stringPtr("Apple MacBook Pro..."),
        //           Price:       2499.99,
        //           Quantity:    25,
        //           Category:    "electronics",
        //           IsActive:    true,
        //       },
        //       // ... more products
        //   }
        $products = [
            [
                'name'        => 'MacBook Pro 16-inch M4 Max',
                'description' => 'The most powerful MacBook Pro ever. Features the M4 Max chip with 16-core CPU, 40-core GPU, and up to 128GB unified memory. Perfect for developers, video editors, and data scientists.',
                'price'       => 2499.99,
                'quantity'    => 25,
                'category'    => 'electronics',
                'is_active'   => true,
            ],
            [
                'name'        => 'Mechanical Keyboard — Cherry MX Blue',
                'description' => 'Premium mechanical keyboard with Cherry MX Blue switches. N-key rollover, RGB backlight, aluminum frame. The satisfying click every developer craves.',
                'price'       => 149.99,
                'quantity'    => 50,
                'category'    => 'electronics',
                'is_active'   => true,
            ],
            [
                'name'        => 'The Go Programming Language (Book)',
                'description' => 'By Alan Donovan and Brian Kernighan. The definitive guide to Go programming. Covers language fundamentals, concurrency patterns, testing, and more. A must-read for every Go developer.',
                'price'       => 39.99,
                'quantity'    => 100,
                'category'    => 'books',
                'is_active'   => true,
            ],
            [
                'name'        => 'Go Developer T-Shirt — Gopher Edition',
                'description' => 'Premium cotton t-shirt featuring the adorable Go Gopher mascot. Available in navy blue. Show your Go pride!',
                'price'       => 29.99,
                'quantity'    => 200,
                'category'    => 'clothing',
                'is_active'   => true,
            ],
            [
                'name'        => 'Artisan Coffee Beans — Ethiopian Yirgacheffe',
                'description' => 'Single-origin, light roast coffee beans. Bright and fruity with notes of blueberry and jasmine. The fuel that powers late-night coding sessions.',
                'price'       => 18.50,
                'quantity'    => 75,
                'category'    => 'food',
                'is_active'   => true,
            ],
            [
                'name'        => 'Standing Desk — Electric Adjustable',
                'description' => 'Ergonomic electric standing desk with memory presets. Height adjustable from 28" to 48". Solid walnut top with steel frame. Your back will thank you.',
                'price'       => 599.00,
                'quantity'    => 15,
                'category'    => 'home',
                'is_active'   => true,
            ],
            [
                'name'        => 'Yoga Mat — Professional Grade',
                'description' => 'Extra thick (6mm) non-slip yoga mat. Perfect for stretching after long coding sessions. Includes carrying strap.',
                'price'       => 45.00,
                'quantity'    => 60,
                'category'    => 'sports',
                'is_active'   => false,  // Inactive product to demonstrate the status feature
            ],
            [
                'name'        => 'Laravel Up and Running (Book)',
                'description' => 'By Matt Stauffer. Comprehensive guide to Laravel framework. Covers routing, Blade templates, Eloquent ORM, testing, and deployment. The perfect companion to this demo!',
                'price'       => 44.99,
                'quantity'    => 80,
                'category'    => 'books',
                'is_active'   => true,
            ],
        ];

        // Loop through each product and insert it.
        //
        // Product::create($productData) inserts a single row.
        //
        // GO EQUIVALENT:
        //   for _, p := range products {
        //       result := db.Create(&p)
        //       if result.Error != nil {
        //           log.Printf("failed to create product: %v", result.Error)
        //       }
        //   }
        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Log a message to the console.
        // In Go: fmt.Println("✅ Seeded 8 products successfully!")
        $this->command->info('✅ Seeded ' . count($products) . ' products successfully!');
    }
}
