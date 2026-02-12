# 🚀 Laravel MVC Demo — For Go Developers

## A complete Laravel CRUD application with extensive Go comparison comments

This project demonstrates Laravel's **Model-View-Controller (MVC)** architecture
with detailed comments comparing every concept to its Go equivalent.

---

## 🏃 Quick Start

```powershell
# Navigate to the project
cd laravel-demo

# Run database migrations and seed sample data
php artisan migrate:fresh --seed

# Start the development server
php artisan serve

# Open in browser: http://127.0.0.1:8000
```

---

## 📁 Project Structure — Laravel vs Go

```
laravel-demo/
│
├── app/                          # ← Go equivalent: internal/ or pkg/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ProductController.php    # ← Go: handlers/product_handler.go
│   └── Models/
│       └── Product.php                  # ← Go: models/product.go (struct)
│
├── database/
│   ├── migrations/
│   │   └── ..._create_products_table.php  # ← Go: migrations/000001_create_products.up.sql
│   └── seeders/
│       ├── DatabaseSeeder.php           # ← Go: cmd/seed/main.go
│       └── ProductSeeder.php            # ← Go: internal/seed/products.go
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php            # ← Go: templates/layouts/base.html
│       └── products/
│           ├── index.blade.php          # ← Go: templates/products/index.html
│           ├── create.blade.php         # ← Go: templates/products/create.html
│           ├── show.blade.php           # ← Go: templates/products/show.html
│           └── edit.blade.php           # ← Go: templates/products/edit.html
│
├── routes/
│   └── web.php                          # ← Go: main.go (router setup in main())
│
├── .env                                 # ← Go: .env or config.yaml
├── artisan                              # ← Go: Makefile or go CLI tool
└── composer.json                        # ← Go: go.mod
```

---

## 🔑 Key Concepts Mapping: Laravel → Go

| Laravel Concept | Go Equivalent | Notes |
|---|---|---|
| `namespace App\Models` | `package models` | PHP uses backslash `\` for namespaces |
| `class Product extends Model` | `type Product struct { gorm.Model }` | Inheritance vs Composition |
| `$fillable = [...]` | `CreateProductInput struct` | Mass assignment protection |
| `Route::resource()` | `r.GET()`, `r.POST()`, etc. | 1 line = 7 routes |
| `php artisan migrate` | `migrate up` | Database schema versioning |
| `php artisan db:seed` | `go run cmd/seed/main.go` | Sample data insertion |
| `@csrf` | manual CSRF token handling | Laravel handles it automatically |
| `@extends('layout')` | `template.ParseFiles(layout, page)` | Template inheritance |
| `{{ $variable }}` | `{{ .Variable }}` | Template variable output |
| `$request->validate()` | `validator.Struct()` | Request validation |
| `Product::create()` | `db.Create(&product)` | Insert into database |
| `Product::find($id)` | `db.First(&product, id)` | Find by primary key |
| `$product->update()` | `db.Save(&product)` | Update existing record |
| `$product->delete()` | `db.Delete(&product)` | Delete record |
| `redirect()->route()` | `http.Redirect()` | HTTP redirect |
| `session('success')` | session flash messages | One-time messages |
| `old('field')` | manual old input handling | Form re-population |
| `compact('products')` | `map[string]interface{}` | Pass data to templates |

---

## 📋 Artisan Commands (Laravel's CLI — like Go's cobra/urfave)

```powershell
# Laravel's artisan is like a Go CLI tool (cobra, urfave/cli)
# Think of it as: go run cmd/artisan/main.go <command>

# Database commands
php artisan migrate              # Create/update database tables
php artisan migrate:rollback     # Undo last migration
php artisan migrate:fresh        # Drop all tables and re-migrate
php artisan db:seed              # Insert sample data

# Code generation (Go doesn't have this — you write everything manually!)
php artisan make:model Product           # Create a model file
php artisan make:controller ProductController  # Create a controller
php artisan make:migration create_products_table  # Create a migration

# Development
php artisan serve                # Start dev server (like: go run main.go)
php artisan route:list           # Show all registered routes
php artisan tinker               # Interactive REPL (like: go playground)
```

---

## 🔄 MVC Request Flow

```
Browser Request: GET /products
        │
        ▼
   ┌─────────────┐
   │  routes/     │  ← Route::resource('products', ProductController::class)
   │  web.php     │     Go: r.GET("/products", handler.Index)
   └──────┬──────┘
          │
          ▼
   ┌─────────────────────────┐
   │  ProductController      │  ← $products = Product::latest()->get()
   │  @index()               │     Go: db.Order("created_at DESC").Find(&products)
   └──────┬──────────────────┘
          │
          ▼
   ┌─────────────────────────┐
   │  Product Model          │  ← Eloquent ORM (translates to SQL)
   │  (Eloquent)             │     Go: GORM (also translates to SQL)
   └──────┬──────────────────┘
          │
          ▼
   ┌─────────────────┐
   │  SQLite Database │  ← SELECT * FROM products ORDER BY created_at DESC
   └──────┬──────────┘
          │
          ▼
   ┌─────────────────────────┐
   │  Blade Template         │  ← products/index.blade.php
   │  (View)                 │     Go: templates/products/index.html
   └──────┬──────────────────┘
          │
          ▼
   HTML Response → Browser
```

---

## 💡 What Makes Laravel Different from Go?

### 1. **Convention over Configuration**
Laravel assumes sensible defaults. Product model → `products` table.
Go makes you explicit about everything.

### 2. **Everything is built-in**
Laravel includes: ORM, routing, templating, authentication, validation, CSRF, sessions, caching, queues, mail, etc.
Go: You pick and assemble packages (net/http + GORM + gorilla/sessions + ...).

### 3. **Artisan CLI**
Laravel has a powerful CLI that generates boilerplate code.
Go: You write everything from scratch (which gives you more control).

### 4. **Magic Methods**
Laravel uses PHP's magic methods (`__get`, `__call`) for features like accessors, scopes, and model binding.
Go: Everything is explicit — no magic, no surprises.

### 5. **Dynamic vs Static Typing**
PHP is dynamically typed (types checked at runtime).
Go is statically typed (types checked at compile time).

---

## 📝 Files to Study (in recommended order)

1. **`routes/web.php`** — Start here. See how URLs map to controller methods.
2. **`app/Http/Controllers/ProductController.php`** — The handler logic.
3. **`app/Models/Product.php`** — The data model (like a Go struct).
4. **`database/migrations/..._create_products_table.php`** — Database schema.
5. **`resources/views/layouts/app.blade.php`** — The master layout template.
6. **`resources/views/products/index.blade.php`** — The product list view.
7. **`resources/views/products/create.blade.php`** — The create form.
8. **`resources/views/products/show.blade.php`** — The detail view.
9. **`resources/views/products/edit.blade.php`** — The edit form.
10. **`database/seeders/ProductSeeder.php`** — Sample data insertion.

---

*Built with ❤️ for Go developers exploring the PHP/Laravel ecosystem.*
