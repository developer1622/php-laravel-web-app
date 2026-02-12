<?php

/**
 * =============================================================================
 * LARAVEL ROUTES FILE (web.php)
 * =============================================================================
 *
 * WHAT ARE ROUTES?
 * ----------------
 * Routes define the URLs your application responds to and connect them
 * to Controllers (or closures/anonymous functions).
 *
 * This file handles "web" routes — routes that return HTML pages.
 * (There's also an api.php for API routes that return JSON.)
 *
 * GO EQUIVALENT:
 * In Go, you set up routes in your main() function or a router setup function:
 *
 *   // Using net/http (standard library):
 *   mux := http.NewServeMux()
 *   mux.HandleFunc("/", homeHandler)
 *   mux.HandleFunc("/products", productHandler.Index)
 *   mux.HandleFunc("/products/create", productHandler.CreateForm)
 *
 *   // Using Gin framework:
 *   r := gin.Default()
 *   r.GET("/", homeHandler)
 *   r.GET("/products", productHandler.Index)
 *   r.GET("/products/create", productHandler.CreateForm)
 *   r.POST("/products", productHandler.Store)
 *   r.GET("/products/:id", productHandler.Show)
 *   r.GET("/products/:id/edit", productHandler.EditForm)
 *   r.PUT("/products/:id", productHandler.Update)
 *   r.DELETE("/products/:id", productHandler.Delete)
 *
 *   // Using gorilla/mux:
 *   r := mux.NewRouter()
 *   r.HandleFunc("/products", productHandler.Index).Methods("GET")
 *   r.HandleFunc("/products", productHandler.Store).Methods("POST")
 *   r.HandleFunc("/products/{id}", productHandler.Show).Methods("GET")
 *
 * KEY DIFFERENCES:
 * 1. Laravel's route file is DECLARATIVE (just define routes, framework handles setup).
 *    Go's routing is IMPERATIVE (you manually set up the router in code).
 * 2. Laravel has "resource routes" — one line generates 7 routes (CRUD).
 *    In Go, you define each route individually.
 * 3. Laravel routes support "named routes" — you can reference them by name.
 *    Go doesn't have this natively (but gorilla/mux supports it).
 *
 * =============================================================================
 */

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/**
 * HOME ROUTE
 * ----------
 * Route::get('/', ...) handles HTTP GET requests to the root URL (/).
 *
 * GO EQUIVALENT:
 *   r.GET("/", func(c *gin.Context) {
 *       c.HTML(200, "welcome.html", nil)
 *   })
 *
 * Or with net/http:
 *   mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
 *       tmpl.ExecuteTemplate(w, "welcome", nil)
 *   })
 *
 * The second argument is a "closure" (anonymous function) — like a Go func literal.
 * It returns a view named 'welcome' (resources/views/welcome.blade.php).
 */
Route::get('/', function () {
    // Redirect the root URL to the products page.
    // In Go: http.Redirect(w, r, "/products", http.StatusFound)
    return redirect()->route('products.index');
});

/**
 * =============================================================================
 * RESOURCE ROUTE — The most powerful routing feature in Laravel!
 * =============================================================================
 *
 * Route::resource('products', ProductController::class) generates ALL 7 RESTful
 * routes for the ProductController in a single line!
 *
 * It creates these routes:
 * +--------+-----------+------------------------+------------------+-----------------------------------+
 * | Method | URI                            | Name             | Controller Method                 |
 * +--------+-----------+------------------------+------------------+-----------------------------------+
 * | GET    | /products                      | products.index   | ProductController@index           |
 * | GET    | /products/create               | products.create  | ProductController@create          |
 * | POST   | /products                      | products.store   | ProductController@store           |
 * | GET    | /products/{product}            | products.show    | ProductController@show            |
 * | GET    | /products/{product}/edit        | products.edit    | ProductController@edit            |
 * | PUT    | /products/{product}            | products.update  | ProductController@update          |
 * | DELETE | /products/{product}            | products.destroy | ProductController@destroy         |
 * +--------+-----------+------------------------+------------------+-----------------------------------+
 *
 * GO EQUIVALENT (you'd have to write all 7 routes manually):
 *
 *   // Using Gin:
 *   products := r.Group("/products")
 *   {
 *       products.GET("/",        productHandler.Index)     // List all
 *       products.GET("/create",  productHandler.CreateForm) // Show create form
 *       products.POST("/",       productHandler.Store)     // Save new product
 *       products.GET("/:id",     productHandler.Show)      // Show single product
 *       products.GET("/:id/edit",productHandler.EditForm)  // Show edit form
 *       products.PUT("/:id",     productHandler.Update)    // Update product
 *       products.DELETE("/:id",  productHandler.Delete)    // Delete product
 *   }
 *
 * This is one of Laravel's biggest productivity features:
 *   1 line in Laravel = 7 lines in Go
 *
 * The {product} in the URI is a route parameter — like :id or {id} in Go.
 * Laravel uses "Route Model Binding" to automatically convert {product} to
 * a Product model instance (queried from the database).
 */
Route::resource('products', ProductController::class);
