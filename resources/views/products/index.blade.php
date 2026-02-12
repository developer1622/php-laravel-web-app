{{--
=============================================================================
PRODUCT INDEX VIEW — List All Products
=============================================================================

This is the "V" (View) in MVC for the product listing page.

BLADE FILE NAMING CONVENTION:
  File: resources/views/products/index.blade.php
  Referenced as: view('products.index')

  The dot (.) maps to directory separator (/):
    products.index → products/index.blade.php

GO EQUIVALENT:
  File: templates/products/index.html
  Referenced as: tmpl.ExecuteTemplate(w, "products/index", data)

KEY CONCEPT — @extends:
  @extends('layouts.app') tells Laravel:
  "Use layouts/app.blade.php as the base, and inject my content into it."

  GO EQUIVALENT:
  In Go, you'd parse multiple template files together:
    tmpl := template.Must(template.ParseFiles(
        "templates/layouts/app.html",
        "templates/products/index.html",
    ))
    tmpl.ExecuteTemplate(w, "layout", data)

=============================================================================
--}}

{{-- Step 1: Extend the layout. This page will be rendered INSIDE the layout. --}}
{{-- GO: template.ParseFiles("layout.html", "index.html") --}}
@extends('layouts.app')

{{-- Step 2: Set the page title (fills @yield('title') in the layout). --}}
{{-- GO: {{ define "title" }}All Products{{ end }} --}}
@section('title', 'All Products')

{{-- Step 3: Define the main content (fills @yield('content') in the layout). --}}
{{-- GO: {{ define "content" }} --}}
@section('content')

    {{-- ================================================== --}}
    {{-- PAGE HEADER with action button                     --}}
    {{-- ================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">📦 All Products</h1>
            <p class="page-subtitle">
                Manage your product inventory — Powered by Laravel Eloquent ORM
            </p>
            <div class="go-comparison">
                <strong>🐹 Go equivalent:</strong> This page is like a Go handler that calls
                <code>db.Order("created_at DESC").Find(&products)</code> and renders
                <code>tmpl.ExecuteTemplate(w, "products/index", data)</code>
            </div>
        </div>

        {{--
            route('products.create') generates: /products/create
            This uses Laravel's named routes.

            GO EQUIVALENT:
            <a href="/products/create">Add New Product</a>
            (Go typically hardcodes URLs or uses a helper function)
        --}}
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            ➕ Add New Product
        </a>
    </div>

    {{-- ================================================== --}}
    {{-- PRODUCTS TABLE or EMPTY STATE                      --}}
    {{-- ================================================== --}}

    {{--
        @if ($products->count() > 0) — Check if there are any products.

        $products is a Laravel "Collection" (similar to a Go slice []Product).
        ->count() returns the number of items (like len(products) in Go).

        GO EQUIVALENT:
        {{ if gt (len .Products) 0 }}
            ... show table ...
        {{ else }}
            ... show empty state ...
        {{ end }}
    --}}
    @if ($products->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{--
                        @foreach — Loop through each product.

                        $products is the data passed from the Controller:
                          return view('products.index', compact('products'));

                        GO EQUIVALENT:
                        {{ range .Products }}
                            <tr>
                                <td>{{ .ID }}</td>
                                <td>{{ .Name }}</td>
                                ...
                            </tr>
                        {{ end }}

                        KEY DIFFERENCE:
                        - In Go: {{ .FieldName }} (dot notation, no $)
                        - In Blade: {{ $product->field_name }} (arrow notation, with $)
                        - In Go, the "dot" (.) changes context inside {{ range }}.
                        - In Blade, $product is explicitly named.
                    --}}
                    @foreach ($products as $product)
                        <tr>
                            {{--
                                {{ $product->id }} — Display the product ID.

                                The {{ }} delimiters in Blade are like Go's {{ }},
                                BUT Blade auto-escapes output (prevents XSS attacks).

                                GO: {{ .ID }}
                                GO (with escaping): {{ .ID }} (Go's html/template also auto-escapes!)

                                $product->id uses PHP's "->" arrow operator for object properties.
                                This is like Go's "." dot operator:
                                  PHP:  $product->id
                                  Go:   product.ID
                            --}}
                            <td>{{ $product->id }}</td>

                            <td class="product-name">{{ $product->name }}</td>

                            {{-- Display category as a styled badge --}}
                            <td>
                                <span class="badge badge-category">
                                    {{ ucfirst($product->category) }}
                                </span>
                            </td>

                            {{--
                                $product->formatted_price calls the ACCESSOR we defined
                                in the Product model (getFormattedPriceAttribute).

                                GO: product.FormattedPrice() — you'd call a method explicitly.
                                Laravel: $product->formatted_price — accessed like a property (magic!).
                            --}}
                            <td class="price-tag">{{ $product->formatted_price }}</td>

                            <td>{{ $product->quantity }}</td>

                            {{--
                                TERNARY OPERATOR in Blade:
                                  $product->is_active ? 'Active' : 'Inactive'

                                GO EQUIVALENT:
                                Go doesn't have ternary operators! You'd use an if-else:
                                  {{ if .IsActive }}Active{{ else }}Inactive{{ end }}

                                Or define a template function:
                                  func statusBadge(active bool) string {
                                      if active { return "Active" }
                                      return "Inactive"
                                  }
                            --}}
                            <td>
                                @if ($product->is_active)
                                    <span class="badge badge-active">✓ Active</span>
                                @else
                                    <span class="badge badge-inactive">✗ Inactive</span>
                                @endif
                            </td>

                            {{-- ACTION BUTTONS --}}
                            <td>
                                <div class="actions">
                                    {{-- View button --}}
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm">
                                        👁
                                    </a>

                                    {{-- Edit button --}}
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                                        ✏️
                                    </a>

                                    {{--
                                        DELETE FORM
                                        -----------
                                        HTML forms only support GET and POST. But REST requires DELETE.
                                        Laravel solves this with @method('DELETE') — a hidden field
                                        that tells Laravel to treat this POST as a DELETE request.

                                        @csrf adds a hidden CSRF token field for security.

                                        GO EQUIVALENT:
                                        In Go, you'd typically:
                                        1. Use JavaScript to send a DELETE request via fetch/XHR:
                                           fetch('/products/5', { method: 'DELETE' })
                                        2. Or use a POST with a "_method" field (like Laravel does)
                                        3. Or use a dedicated delete endpoint: POST /products/5/delete

                                        CSRF in Go:
                                        You'd use gorilla/csrf middleware:
                                          <input type="hidden" name="csrf_token" value="{{ .CSRFToken }}">
                                    --}}
                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          style="display:inline"
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Product count summary --}}
        <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.85rem;">
            {{--
                $products->count() — Returns the number of products.
                GO: len(products)
            --}}
            Showing {{ $products->count() }} product(s)
        </p>

    @else
        {{-- EMPTY STATE — shown when there are no products --}}
        <div class="card empty-state">
            <div class="empty-state-icon">📦</div>
            <h2 class="empty-state-title">No products yet</h2>
            <p class="empty-state-text">
                Start by adding your first product to the inventory.
            </p>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                ➕ Create Your First Product
            </a>
        </div>
    @endif

{{-- End of @section('content') --}}
{{-- GO: {{ end }} — closes the {{ define "content" }} block --}}
@endsection
