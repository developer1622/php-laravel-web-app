<?php

/**
 * =============================================================================
 * LARAVEL CONTROLLER — The "C" in MVC (Model-View-Controller)
 * =============================================================================
 *
 * WHAT IS A CONTROLLER?
 * ---------------------
 * A Controller handles HTTP requests, processes data (using Models),
 * and returns responses (using Views or JSON).
 *
 * Think of it as the "middleman":
 *   HTTP Request → Controller → Model (get/save data) → View (render HTML) → HTTP Response
 *
 * GO EQUIVALENT:
 * In Go, a "controller" is typically called a "handler".
 * It's a function (or method) that takes http.ResponseWriter and *http.Request:
 *
 *   // handler.go
 *   package handlers
 *
 *   func ListProducts(w http.ResponseWriter, r *http.Request) {
 *       products, err := productRepo.FindAll()
 *       if err != nil {
 *           http.Error(w, "Internal Server Error", 500)
 *           return
 *       }
 *       tmpl.Execute(w, products)
 *   }
 *
 * Or with a framework like Gin:
 *   func ListProducts(c *gin.Context) {
 *       products, err := productRepo.FindAll()
 *       if err != nil {
 *           c.JSON(500, gin.H{"error": "Internal Server Error"})
 *           return
 *       }
 *       c.HTML(200, "products/index.html", gin.H{"products": products})
 *   }
 *
 * KEY DIFFERENCES:
 * 1. Go handlers are functions. Laravel controllers are class methods.
 * 2. Go returns errors explicitly. Laravel throws exceptions.
 * 3. Go uses templates (html/template). Laravel uses Blade templates.
 * 4. Laravel controllers auto-inject dependencies (Dependency Injection).
 *    In Go, you manually pass dependencies to handlers.
 *
 * =============================================================================
 */

// namespace = package declaration
// In Go: package controllers
// In PHP: namespace App\Http\Controllers;
namespace App\Http\Controllers;

// Importing the Product model and Request class.
// In Go: import "myapp/models"
// In PHP: use App\Models\Product;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * ProductController handles all HTTP requests related to Products.
 *
 * "extends Controller" — inherits from the base Controller class.
 *
 * GO EQUIVALENT:
 * In Go, you'd create a struct with dependencies injected:
 *
 *   type ProductHandler struct {
 *       db   *gorm.DB           // Database connection
 *       tmpl *template.Template // Template engine
 *   }
 *
 *   func NewProductHandler(db *gorm.DB, tmpl *template.Template) *ProductHandler {
 *       return &ProductHandler{db: db, tmpl: tmpl}
 *   }
 *
 * Laravel does dependency injection automatically via its "Service Container".
 * In Go, you do it manually (or use a DI library like google/wire).
 */
class ProductController extends Controller
{
    /**
     * ==========================================================================
     * INDEX — Display a list of all products.
     * ==========================================================================
     *
     * HTTP: GET /products
     *
     * This is the main listing page. It fetches all products from the database
     * and passes them to a Blade view for rendering.
     *
     * GO EQUIVALENT (with Gin framework):
     *
     *   // GET /products
     *   func (h *ProductHandler) Index(c *gin.Context) {
     *       var products []models.Product
     *
     *       // Fetch all products, ordered by latest first
     *       result := h.db.Order("created_at DESC").Find(&products)
     *       if result.Error != nil {
     *           c.HTML(500, "error.html", gin.H{"error": "Failed to fetch products"})
     *           return
     *       }
     *
     *       // Render the template with products data
     *       c.HTML(200, "products/index.html", gin.H{
     *           "products": products,
     *           "title":    "All Products",
     *       })
     *   }
     *
     * GO EQUIVALENT (with net/http):
     *
     *   func (h *ProductHandler) Index(w http.ResponseWriter, r *http.Request) {
     *       products, err := h.repo.FindAll()
     *       if err != nil {
     *           http.Error(w, "Internal Server Error", 500)
     *           return
     *       }
     *       h.tmpl.ExecuteTemplate(w, "products/index", map[string]interface{}{
     *           "products": products,
     *       })
     *   }
     */
    public function index()
    {
        // Product::latest() orders by 'created_at' DESC (newest first).
        // ->get() executes the query and returns a Collection (like a Go slice).
        //
        // GO/GORM: db.Order("created_at DESC").Find(&products)
        //
        // The resulting $products is a Laravel "Collection" — think of it like
        // a Go slice ([]Product) but with MANY helper methods attached:
        //   $products->count()    // like len(products) in Go
        //   $products->first()    // like products[0] in Go
        //   $products->filter()   // like filtering a slice with a loop in Go
        //   $products->map()      // like a for-range loop that transforms each item
        $products = Product::latest()->get();

        // return view('products.index', [...]) renders a Blade template.
        //
        // 'products.index' means: resources/views/products/index.blade.php
        // The dot (.) maps to directory separator (/).
        //
        // The second argument is an associative array (like a Go map[string]interface{})
        // that passes data to the template.
        //
        // GO EQUIVALENT:
        //   tmpl.ExecuteTemplate(w, "products/index.html", map[string]interface{}{
        //       "products": products,
        //       "title":    "Product List",
        //   })
        //
        // compact('products') is a PHP shortcut that creates:
        //   ['products' => $products]
        // It takes the variable NAME as a string and creates a key-value pair.
        // There's no Go equivalent — in Go you'd explicitly build the map.
        return view('products.index', compact('products'));
    }

    /**
     * ==========================================================================
     * CREATE — Show the form to create a new product.
     * ==========================================================================
     *
     * HTTP: GET /products/create
     *
     * This just shows an empty HTML form. No database interaction here.
     *
     * GO EQUIVALENT:
     *   func (h *ProductHandler) CreateForm(c *gin.Context) {
     *       c.HTML(200, "products/create.html", nil)
     *   }
     *
     * Or with net/http:
     *   func (h *ProductHandler) CreateForm(w http.ResponseWriter, r *http.Request) {
     *       h.tmpl.ExecuteTemplate(w, "products/create", nil)
     *   }
     */
    public function create()
    {
        // Simply return the view with the form.
        // No data needed — just render the empty form.
        return view('products.create');
    }

    /**
     * ==========================================================================
     * STORE — Save a new product to the database.
     * ==========================================================================
     *
     * HTTP: POST /products
     *
     * This method:
     * 1. Validates the incoming request data
     * 2. Creates a new product in the database
     * 3. Redirects to the product list with a success message
     *
     * GO EQUIVALENT (with Gin):
     *
     *   type CreateProductInput struct {
     *       Name        string  `form:"name" binding:"required,max=255"`
     *       Description string  `form:"description"`
     *       Price       float64 `form:"price" binding:"required,gte=0"`
     *       Quantity    int     `form:"quantity" binding:"gte=0"`
     *       Category    string  `form:"category" binding:"required"`
     *       IsActive    bool    `form:"is_active"`
     *   }
     *
     *   func (h *ProductHandler) Store(c *gin.Context) {
     *       var input CreateProductInput
     *
     *       // Bind and validate
     *       if err := c.ShouldBind(&input); err != nil {
     *           c.HTML(422, "products/create.html", gin.H{"errors": err.Error()})
     *           return
     *       }
     *
     *       product := models.Product{
     *           Name:        input.Name,
     *           Description: &input.Description,
     *           Price:       input.Price,
     *           Quantity:    input.Quantity,
     *           Category:    input.Category,
     *           IsActive:    input.IsActive,
     *       }
     *
     *       if err := h.db.Create(&product).Error; err != nil {
     *           c.HTML(500, "products/create.html", gin.H{"error": "Failed to create"})
     *           return
     *       }
     *
     *       // Redirect with flash message
     *       c.Redirect(302, "/products")
     *   }
     *
     * @param Request $request — This is auto-injected by Laravel (Dependency Injection).
     *                           It contains all HTTP request data (form fields, headers, etc.).
     *                           GO EQUIVALENT: *http.Request or *gin.Context
     */
    public function store(Request $request)
    {
        // STEP 1: VALIDATE the incoming request.
        //
        // $request->validate([...]) checks the form data against rules.
        // If validation fails, Laravel automatically:
        //   - Redirects back to the form
        //   - Flashes error messages to the session
        //   - Flashes old input data (so the form can re-populate)
        //
        // GO EQUIVALENT (manual validation):
        //   if input.Name == "" {
        //       errors = append(errors, "name is required")
        //   }
        //   if input.Price < 0 {
        //       errors = append(errors, "price must be >= 0")
        //   }
        //
        // Or with a validation library (github.com/go-playground/validator):
        //   validate := validator.New()
        //   err := validate.Struct(input)
        //
        // KEY DIFFERENCE:
        // Laravel validation is declarative (you describe rules as strings).
        // Go validation is typically imperative (you write if-else checks)
        // or uses struct tags with a validator library.
        $validated = $request->validate([
            'name'        => 'required|string|max:255',    // Must exist, be a string, max 255 chars
            'description' => 'nullable|string',             // Optional, but if present, must be string
            'price'       => 'required|numeric|min:0',      // Must exist, be a number, >= 0
            'quantity'    => 'required|integer|min:0',       // Must exist, be integer, >= 0
            'category'    => 'required|string|max:100',     // Must exist, be a string, max 100 chars
        ]);

        // STEP 2: Handle the checkbox.
        // HTML checkboxes only send data when checked. If unchecked, they send nothing.
        // $request->has('is_active') returns true if the checkbox was checked.
        //
        // GO EQUIVALENT:
        //   isActive := r.FormValue("is_active") == "on" || r.FormValue("is_active") == "1"
        $validated['is_active'] = $request->has('is_active');

        // STEP 3: CREATE the product in the database.
        //
        // Product::create($validated) does:
        //   1. Creates a new Product instance
        //   2. Sets all fields from $validated array (only $fillable fields are allowed!)
        //   3. Inserts it into the 'products' table
        //   4. Returns the new Product instance with its ID
        //
        // GO/GORM EQUIVALENT:
        //   product := models.Product{
        //       Name:     validated.Name,
        //       Price:    validated.Price,
        //       // ... other fields
        //   }
        //   result := db.Create(&product)
        //   if result.Error != nil {
        //       // handle error
        //   }
        Product::create($validated);

        // STEP 4: REDIRECT to the product list with a success flash message.
        //
        // redirect()->route('products.index') generates the URL for the named route.
        // ->with('success', '...') stores a one-time "flash" message in the session.
        //
        // GO EQUIVALENT:
        //   // Set flash message in session
        //   session.Flash(r, "success", "Product created successfully!")
        //   // Redirect
        //   http.Redirect(w, r, "/products", http.StatusFound)
        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * ==========================================================================
     * SHOW — Display a single product's details.
     * ==========================================================================
     *
     * HTTP: GET /products/{id}
     *
     * Laravel's "Route Model Binding" automatically:
     *   1. Takes the {id} from the URL (e.g., /products/5)
     *   2. Queries: SELECT * FROM products WHERE id = 5
     *   3. Injects the Product instance into this method
     *   4. Returns 404 if not found
     *
     * GO EQUIVALENT:
     *   func (h *ProductHandler) Show(c *gin.Context) {
     *       // Get ID from URL parameter
     *       id := c.Param("id")
     *
     *       // Query the database
     *       var product models.Product
     *       result := h.db.First(&product, id)
     *       if result.Error != nil {
     *           c.HTML(404, "errors/404.html", nil)
     *           return
     *       }
     *
     *       c.HTML(200, "products/show.html", gin.H{"product": product})
     *   }
     *
     * NOTICE: In Laravel, the $product parameter is automatically populated!
     * This is called "Dependency Injection" + "Route Model Binding".
     * In Go, you have to manually extract the ID and query the database.
     *
     * @param Product $product — Auto-injected by Laravel's Route Model Binding
     */
    public function show(Product $product)
    {
        // $product is already loaded from the database!
        // We just pass it to the view.
        //
        // compact('product') creates: ['product' => $product]
        // Same as: return view('products.show', ['product' => $product]);
        return view('products.show', compact('product'));
    }

    /**
     * ==========================================================================
     * EDIT — Show the form to edit an existing product.
     * ==========================================================================
     *
     * HTTP: GET /products/{id}/edit
     *
     * Similar to create(), but pre-fills the form with existing data.
     *
     * GO EQUIVALENT:
     *   func (h *ProductHandler) EditForm(c *gin.Context) {
     *       id := c.Param("id")
     *       var product models.Product
     *       if err := h.db.First(&product, id).Error; err != nil {
     *           c.HTML(404, "errors/404.html", nil)
     *           return
     *       }
     *       c.HTML(200, "products/edit.html", gin.H{"product": product})
     *   }
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * ==========================================================================
     * UPDATE — Save changes to an existing product.
     * ==========================================================================
     *
     * HTTP: PUT /products/{id}
     *
     * Similar to store(), but updates an existing record instead of creating.
     *
     * GO EQUIVALENT:
     *   func (h *ProductHandler) Update(c *gin.Context) {
     *       id := c.Param("id")
     *       var product models.Product
     *       if err := h.db.First(&product, id).Error; err != nil {
     *           c.HTML(404, "errors/404.html", nil)
     *           return
     *       }
     *
     *       var input UpdateProductInput
     *       if err := c.ShouldBind(&input); err != nil {
     *           c.HTML(422, "products/edit.html", gin.H{"errors": err.Error()})
     *           return
     *       }
     *
     *       // Update fields
     *       product.Name = input.Name
     *       product.Price = input.Price
     *       // ... other fields
     *
     *       if err := h.db.Save(&product).Error; err != nil {
     *           c.HTML(500, "products/edit.html", gin.H{"error": "Failed to update"})
     *           return
     *       }
     *
     *       c.Redirect(302, fmt.Sprintf("/products/%d", product.ID))
     *   }
     */
    public function update(Request $request, Product $product)
    {
        // Validate (same rules as store)
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'category'    => 'required|string|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // $product->update($validated) does:
        //   UPDATE products SET name=?, price=?, ... WHERE id = ?
        //
        // GO/GORM EQUIVALENT:
        //   db.Model(&product).Updates(models.Product{
        //       Name:  validated.Name,
        //       Price: validated.Price,
        //   })
        //
        // Or:
        //   db.Save(&product) // Saves all fields
        $product->update($validated);

        // Redirect to the product detail page.
        // route('products.show', $product) generates: /products/{id}
        //
        // GO EQUIVALENT:
        //   http.Redirect(w, r, fmt.Sprintf("/products/%d", product.ID), 302)
        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * ==========================================================================
     * DESTROY — Delete a product from the database.
     * ==========================================================================
     *
     * HTTP: DELETE /products/{id}
     *
     * GO EQUIVALENT:
     *   func (h *ProductHandler) Delete(c *gin.Context) {
     *       id := c.Param("id")
     *       if err := h.db.Delete(&models.Product{}, id).Error; err != nil {
     *           c.JSON(500, gin.H{"error": "Failed to delete"})
     *           return
     *       }
     *       c.Redirect(302, "/products")
     *   }
     */
    public function destroy(Product $product)
    {
        // $product->delete() executes: DELETE FROM products WHERE id = ?
        //
        // GO/GORM: db.Delete(&product)
        // Or:      db.Delete(&models.Product{}, id)
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
