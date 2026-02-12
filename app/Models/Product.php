<?php

/**
 * =============================================================================
 * LARAVEL MODEL — The "M" in MVC (Model-View-Controller)
 * =============================================================================
 *
 * WHAT IS A MODEL?
 * ----------------
 * A Model represents a single database table and provides methods to
 * interact with that table (create, read, update, delete records).
 *
 * Laravel uses an ORM called "Eloquent" — it maps PHP classes to database tables.
 * Each instance of this class = one row in the "products" table.
 *
 * GO EQUIVALENT:
 * In Go, a "model" is typically just a struct:
 *
 *   // product.go
 *   package models
 *
 *   import "time"
 *
 *   type Product struct {
 *       ID          uint      `json:"id" gorm:"primaryKey"`
 *       Name        string    `json:"name" gorm:"not null"`
 *       Description *string   `json:"description"`
 *       Price       float64   `json:"price" gorm:"type:decimal(10,2)"`
 *       Quantity    int       `json:"quantity" gorm:"default:0"`
 *       Category    string    `json:"category" gorm:"default:'general'"`
 *       IsActive    bool      `json:"is_active" gorm:"default:true"`
 *       CreatedAt   time.Time `json:"created_at"`
 *       UpdatedAt   time.Time `json:"updated_at"`
 *   }
 *
 * KEY DIFFERENCE:
 * - In Go, a struct is just data. You need separate functions to do DB operations.
 * - In Laravel, the Model class ITSELF has built-in methods like save(), delete(),
 *   find(), where(), etc. This is the "Active Record" pattern.
 *
 * Go typically uses the "Repository" pattern instead:
 *   type ProductRepository interface {
 *       FindAll() ([]Product, error)
 *       FindByID(id uint) (*Product, error)
 *       Create(p *Product) error
 *       Update(p *Product) error
 *       Delete(id uint) error
 *   }
 *
 * =============================================================================
 */

// "namespace" is like Go's "package" declaration.
// In Go: package models
// In PHP: namespace App\Models;
//
// KEY DIFFERENCE:
// - Go uses directory-based packages: all .go files in a folder share the same package.
// - PHP uses namespace declarations at the top of each file (can be anything, but
//   by convention, matches the directory structure).
namespace App\Models;

// "use" imports classes from other namespaces.
// In Go: import "github.com/jinzhu/gorm"
// In PHP: use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 *
 * "extends Model" means Product inherits from Eloquent's base Model class.
 * This gives Product all the database query methods (find, where, create, etc.).
 *
 * GO EQUIVALENT:
 * Go doesn't have inheritance. Instead, you'd use composition (embedding):
 *
 *   type Product struct {
 *       gorm.Model  // Embedded struct — gives ID, CreatedAt, UpdatedAt, DeletedAt
 *       Name        string
 *       Description *string
 *       // ... other fields
 *   }
 *
 * The gorm.Model embed is similar to "extends Model" in Laravel.
 * Both give you common fields (ID, timestamps) and ORM capabilities.
 */
class Product extends Model
{
    /**
     * "use HasFactory" is a PHP Trait — it's like a "mixin" that adds methods
     * to this class without inheritance.
     *
     * GO EQUIVALENT:
     * Go doesn't have traits, but the closest thing is embedding an interface
     * or using composition:
     *
     *   type HasFactory struct{}
     *   func (h HasFactory) Factory() *Factory { ... }
     *
     *   type Product struct {
     *       HasFactory  // Embed — now Product has Factory() method
     *   }
     *
     * Traits let you "copy-paste" methods into a class.
     * HasFactory adds a factory() method for generating test data.
     */
    use HasFactory;

    /**
     * ==========================================================================
     * TABLE NAME (Convention over Configuration)
     * ==========================================================================
     *
     * By default, Laravel assumes the table name is the plural, snake_case
     * version of the class name:
     *   Product -> products
     *   BlogPost -> blog_posts
     *   User -> users
     *
     * You can override this with: protected $table = 'my_custom_table';
     *
     * GO EQUIVALENT (GORM):
     * GORM does the same thing! It pluralizes the struct name:
     *   type Product struct {} -> table "products"
     *
     * To override in GORM:
     *   func (Product) TableName() string {
     *       return "my_custom_table"
     *   }
     */
    // We don't need to set $table here because "Product" -> "products" is correct.
    // protected $table = 'products';

    /**
     * ==========================================================================
     * MASS ASSIGNMENT PROTECTION ($fillable)
     * ==========================================================================
     *
     * $fillable defines which columns can be set via mass assignment.
     * Mass assignment means setting multiple fields at once, like:
     *   Product::create(['name' => 'Laptop', 'price' => 999.99]);
     *
     * This is a SECURITY feature — it prevents users from setting fields
     * they shouldn't (like 'id' or 'is_admin').
     *
     * GO EQUIVALENT:
     * Go doesn't have built-in mass assignment protection.
     * You'd typically handle this in your handler/controller:
     *
     *   type CreateProductRequest struct {
     *       Name        string  `json:"name" validate:"required"`
     *       Description string  `json:"description"`
     *       Price       float64 `json:"price" validate:"required,gte=0"`
     *       Quantity    int     `json:"quantity" validate:"gte=0"`
     *       Category    string  `json:"category"`
     *   }
     *
     *   // Then map ONLY the allowed fields:
     *   product := models.Product{
     *       Name:        req.Name,
     *       Description: &req.Description,
     *       Price:       req.Price,
     *   }
     *
     * In Go, you control this by defining what fields your request struct accepts.
     * Laravel's $fillable does the same thing but at the model level.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category',
        'is_active',
    ];

    /**
     * ==========================================================================
     * ATTRIBUTE CASTING ($casts)
     * ==========================================================================
     *
     * $casts tells Laravel to automatically convert database values to
     * specific PHP types when you access them.
     *
     * For example:
     *   'price' => 'decimal:2'  means $product->price returns "99.99" (string with 2 decimals)
     *   'is_active' => 'boolean' means $product->is_active returns true/false (not 0/1)
     *
     * GO EQUIVALENT:
     * In Go, the struct field types handle this naturally:
     *   Price    float64 `json:"price"`     // Already a float
     *   IsActive bool    `json:"is_active"` // Already a bool
     *
     * Go's type system is stricter — types are defined at compile time.
     * PHP is dynamically typed, so casting helps enforce types at runtime.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',     // Ensures price always has 2 decimal places
            'is_active' => 'boolean',   // Converts 0/1 to false/true
            'quantity' => 'integer',    // Ensures quantity is always an integer
        ];
    }

    /**
     * ==========================================================================
     * ACCESSOR (Computed Property / Getter)
     * ==========================================================================
     *
     * An accessor lets you define a "virtual" attribute that doesn't exist
     * in the database but is computed from other attributes.
     *
     * This is called automatically when you access: $product->formatted_price
     *
     * GO EQUIVALENT:
     * In Go, you'd define a method on the struct:
     *
     *   func (p *Product) FormattedPrice() string {
     *       return fmt.Sprintf("$%.2f", p.Price)
     *   }
     *
     * Usage:
     *   product.FormattedPrice() // "$99.99"
     *
     * KEY DIFFERENCE:
     * - Go: You explicitly call a method: product.FormattedPrice()
     * - Laravel: You access it like a property: $product->formatted_price
     *            Laravel "magically" calls this method behind the scenes.
     *            This "magic" is done via PHP's __get() magic method.
     */
    public function getFormattedPriceAttribute(): string
    {
        // $this->price refers to the 'price' column value.
        // In Go: p.Price (where p is the receiver *Product)
        return '$' . number_format($this->price, 2);
    }

    /**
     * ==========================================================================
     * SCOPE (Reusable Query Filter)
     * ==========================================================================
     *
     * A "scope" is a reusable query condition you can chain onto queries.
     * Usage: Product::active()->get()  — gets only active products.
     *
     * The "scope" prefix is a Laravel convention:
     *   Method name: scopeActive
     *   Usage: Product::active() (Laravel strips the "scope" prefix)
     *
     * GO EQUIVALENT:
     * In Go, you'd typically use a query builder or repository method:
     *
     *   // Using GORM scopes:
     *   func Active(db *gorm.DB) *gorm.DB {
     *       return db.Where("is_active = ?", true)
     *   }
     *
     *   // Usage:
     *   db.Scopes(Active).Find(&products)
     *
     *   // Or as a repository method:
     *   func (r *ProductRepo) FindActive() ([]Product, error) {
     *       var products []Product
     *       err := r.db.Where("is_active = ?", true).Find(&products).Error
     *       return products, err
     *   }
     */
    public function scopeActive($query)
    {
        // $query is the query builder instance.
        // ->where('is_active', true) adds a WHERE clause.
        // GO/GORM: db.Where("is_active = ?", true)
        return $query->where('is_active', true);
    }

    /**
     * Another scope example — filter by category.
     * Usage: Product::byCategory('electronics')->get()
     *
     * GO EQUIVALENT:
     *   func ByCategory(category string) func(db *gorm.DB) *gorm.DB {
     *       return func(db *gorm.DB) *gorm.DB {
     *           return db.Where("category = ?", category)
     *       }
     *   }
     *   // Usage: db.Scopes(ByCategory("electronics")).Find(&products)
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
