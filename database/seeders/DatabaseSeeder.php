<?php

/**
 * =============================================================================
 * DATABASE SEEDER — The Master Seeder
 * =============================================================================
 *
 * WHAT IS DATABASE SEEDER?
 * ------------------------
 * This is the "main" seeder. When you run "php artisan db:seed", THIS file
 * is executed. It orchestrates calling other seeders.
 *
 * GO EQUIVALENT:
 * This is like a main() function that calls other seed functions:
 *
 *   func main() {
 *       db := connectDB()
 *       seedUsers(db)
 *       seedProducts(db)
 *       fmt.Println("All seeders completed!")
 *   }
 *
 * HOW TO RUN:
 *   php artisan db:seed              — Runs this file
 *   php artisan migrate:fresh --seed — Drops all tables, re-runs migrations, then seeds
 *
 * GO EQUIVALENT:
 *   go run cmd/seed/main.go
 *
 * =============================================================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * $this->call() runs another seeder class.
     * You can pass an array of seeder classes to run them in order.
     *
     * GO EQUIVALENT:
     *   func (s *DatabaseSeeder) Run(db *gorm.DB) {
     *       productSeeder := &ProductSeeder{db: db}
     *       productSeeder.Run()
     *   }
     */
    public function run(): void
    {
        // Call the ProductSeeder to insert sample products.
        // $this->call() is like calling a function in Go:
        //   productSeeder.Run()
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
