<?php

/**
 * =============================================================================
 * LARAVEL MIGRATION FILE
 * =============================================================================
 *
 * WHAT IS A MIGRATION?
 * --------------------
 * A migration is like a "version control" for your database schema.
 * Each migration file represents a change to your database (create table,
 * add column, drop table, etc.).
 *
 * GO EQUIVALENT:
 * In Go, you'd typically use tools like:
 *   - golang-migrate/migrate (CLI tool with .sql files)
 *   - pressly/goose
 *   - GORM's AutoMigrate()
 *
 * In Go with golang-migrate, you'd write raw SQL files like:
 *   // 000001_create_products_table.up.sql
 *   CREATE TABLE products (
 *       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *       name VARCHAR(255) NOT NULL,
 *       ...
 *   );
 *
 * In Laravel, you write PHP code instead of raw SQL.
 * Laravel converts this PHP code to the correct SQL for your database
 * (MySQL, PostgreSQL, SQLite, etc.) — this is called "database abstraction".
 *
 * HOW TO RUN THIS MIGRATION:
 *   php artisan migrate
 *
 * GO EQUIVALENT COMMAND:
 *   migrate -path ./migrations -database "sqlite3://database.db" up
 *
 * =============================================================================
 */

// These are "use" statements — similar to "import" in Go.
// In Go:  import "github.com/some/package"
// In PHP: use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "return new class extends Migration" — This is an anonymous class.
 *
 * GO EQUIVALENT:
 * Go doesn't have anonymous classes, but the concept is similar to
 * implementing an interface inline. In Go, you'd create a struct
 * that satisfies a Migrator interface:
 *
 *   type Migrator interface {
 *       Up() error
 *       Down() error
 *   }
 *
 * The "extends Migration" part is like embedding a base struct in Go:
 *   type ProductMigration struct {
 *       BaseMigration  // Go's composition (embedding) instead of inheritance
 *   }
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method is called when you run: php artisan migrate
     * It creates the table in your database.
     *
     * GO EQUIVALENT:
     *   func (m *ProductMigration) Up() error {
     *       // Execute CREATE TABLE SQL
     *       _, err := db.Exec(`CREATE TABLE products (...)`)
     *       return err
     *   }
     *
     * Or with GORM:
     *   db.AutoMigrate(&Product{})
     */
    public function up(): void
    {
        // Schema::create() creates a new table.
        // The first argument is the table name ('products').
        // The second argument is a "closure" (anonymous function) — like a Go func literal.
        //
        // GO EQUIVALENT of a closure:
        //   func(table *schema.Blueprint) { ... }
        //
        // Blueprint $table is like a "builder" that lets you define columns.
        // It's similar to GORM's model tags in Go:
        //   type Product struct {
        //       ID          uint   `gorm:"primaryKey;autoIncrement"`
        //       Name        string `gorm:"size:255;not null"`
        //       Description string `gorm:"type:text"`
        //       Price       float64 `gorm:"type:decimal(10,2);not null"`
        //   }
        Schema::create('products', function (Blueprint $table) {

            // $table->id() — Auto-incrementing BIGINT primary key.
            // GO/GORM: ID uint `gorm:"primaryKey;autoIncrement"`
            $table->id();

            // $table->string('name') — VARCHAR(255) column.
            // GO/GORM: Name string `gorm:"size:255;not null"`
            $table->string('name');

            // $table->text('description') — TEXT column (no size limit like VARCHAR).
            // GO/GORM: Description string `gorm:"type:text"`
            // ->nullable() means this column CAN be NULL (optional field).
            // GO/GORM: Description *string `gorm:"type:text"` (pointer = nullable)
            $table->text('description')->nullable();

            // $table->decimal('price', 10, 2) — DECIMAL(10,2) for money.
            // 10 = total digits, 2 = decimal places. Example: 99999999.99
            // GO/GORM: Price float64 `gorm:"type:decimal(10,2);not null;default:0"`
            $table->decimal('price', 10, 2)->default(0);

            // $table->integer('quantity') — INTEGER column.
            // GO/GORM: Quantity int `gorm:"not null;default:0"`
            $table->integer('quantity')->default(0);

            // $table->string('category') — Another VARCHAR(255) column.
            // GO/GORM: Category string `gorm:"size:255;default:'general'"`
            $table->string('category')->default('general');

            // $table->boolean('is_active') — BOOLEAN column (TINYINT in MySQL).
            // GO/GORM: IsActive bool `gorm:"default:true"`
            $table->boolean('is_active')->default(true);

            // $table->timestamps() — Adds TWO columns: created_at and updated_at.
            // These are automatically managed by Laravel (Eloquent ORM).
            // GO/GORM: CreatedAt time.Time `gorm:"autoCreateTime"`
            //          UpdatedAt time.Time `gorm:"autoUpdateTime"`
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method is called when you run: php artisan migrate:rollback
     * It UNDOES whatever up() did — in this case, drops the table.
     *
     * GO EQUIVALENT:
     *   func (m *ProductMigration) Down() error {
     *       _, err := db.Exec(`DROP TABLE IF EXISTS products`)
     *       return err
     *   }
     *
     * Or the corresponding .down.sql file:
     *   DROP TABLE IF EXISTS products;
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
