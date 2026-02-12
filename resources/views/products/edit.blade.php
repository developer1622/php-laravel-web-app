{{--
=============================================================================
PRODUCT EDIT VIEW — Form to Edit an Existing Product
=============================================================================

This view is almost identical to create.blade.php, but:
1. Form action points to UPDATE route (not STORE)
2. Uses @method('PUT') since HTML forms don't support PUT
3. Fields are pre-filled with existing product data

GO EQUIVALENT:
  Same as create.html but with pre-filled values:
    <input value="{{ .Product.Name }}">

  And the form uses a different method:
    // Option 1: JavaScript fetch with PUT
    fetch(`/products/${product.ID}`, { method: 'PUT', body: formData })

    // Option 2: Hidden _method field (like Laravel)
    <input type="hidden" name="_method" value="PUT">

=============================================================================
--}}

@extends('layouts.app')

@section('title', 'Edit: ' . $product->name)

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">✏️ Edit: {{ $product->name }}</h1>
            <p class="page-subtitle">Update the product details below.</p>
            <div class="go-comparison">
                <strong>🐹 Go equivalent:</strong> This is like a handler that:
                <br>1. GET: Fetches product with <code>db.First(&product, id)</code> and renders form
                <br>2. PUT: Processes <code>r.ParseForm()</code>, validates, then <code>db.Save(&product)</code>
            </div>
        </div>
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline">
            ← Back to Product
        </a>
    </div>

    <div class="card">
        {{--
            FORM: Note the differences from create.blade.php:

            1. action="{{ route('products.update', $product) }}"
               → Points to: PUT /products/{id}
               → GO: <form action="/products/{{ .Product.ID }}" method="POST">

            2. @method('PUT')
               → Generates: <input type="hidden" name="_method" value="PUT">
               → This tells Laravel to treat this POST as a PUT request.
               → GO: You'd handle this manually or use JavaScript.

            3. All inputs have value="{{ old('field', $product->field) }}"
               → old('field', $product->field) means:
                 - If there's old input (validation failed), use that.
                 - Otherwise, use the current product value.
               → GO: You'd need to check for old input first, then fall back to product data.
        --}}
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf

            {{--
                @method('PUT') — Method Spoofing
                HTML forms can only do GET and POST.
                This hidden field tells Laravel's router to treat this as PUT.

                GO EQUIVALENT:
                <input type="hidden" name="_method" value="PUT">
                Then in your Go router middleware:
                  func methodOverride(next http.Handler) http.Handler {
                      return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
                          if r.Method == "POST" {
                              if method := r.FormValue("_method"); method != "" {
                                  r.Method = strings.ToUpper(method)
                              }
                          }
                          next.ServeHTTP(w, r)
                      })
                  }

                Gin framework has this built-in with c.Request.Method.
            --}}
            @method('PUT')

            {{-- PRODUCT NAME --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    Product Name <span style="color: var(--accent-danger);">*</span>
                </label>
                {{--
                    old('name', $product->name)
                    → First tries: session's old input (if validation failed)
                    → Falls back to: $product->name (current database value)

                    GO EQUIVALENT:
                    value := oldInput.Name
                    if value == "" {
                        value = product.Name
                    }
                    // In template: <input value="{{ .Value }}">
                --}}
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $product->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="e.g., MacBook Pro 16-inch"
                       required>
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- DESCRIPTION --}}
            <div class="form-group">
                <label for="description" class="form-label">
                    Description
                    <span class="form-hint">(optional)</span>
                </label>
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe the product...">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- PRICE & QUANTITY --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">
                        Price ($) <span style="color: var(--accent-danger);">*</span>
                    </label>
                    <input type="number"
                           id="price"
                           name="price"
                           value="{{ old('price', $product->price) }}"
                           class="form-control @error('price') is-invalid @enderror"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           required>
                    @error('price')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity" class="form-label">
                        Quantity <span style="color: var(--accent-danger);">*</span>
                    </label>
                    <input type="number"
                           id="quantity"
                           name="quantity"
                           value="{{ old('quantity', $product->quantity) }}"
                           class="form-control @error('quantity') is-invalid @enderror"
                           min="0"
                           placeholder="0"
                           required>
                    @error('quantity')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="form-group">
                <label for="category" class="form-label">
                    Category <span style="color: var(--accent-danger);">*</span>
                </label>
                {{--
                    For select dropdowns with old() + current value:
                    old('category', $product->category) returns:
                      - Old input if validation failed
                      - Current product category otherwise

                    Then we compare with == to set "selected".

                    PHP TRICK: The @php $selectedCategory = ... @endphp
                    lets us set a local variable to avoid repeating the old() call.

                    GO EQUIVALENT:
                    selectedCategory := oldInput.Category
                    if selectedCategory == "" {
                        selectedCategory = product.Category
                    }
                --}}
                @php
                    $selectedCategory = old('category', $product->category);
                @endphp
                <select id="category"
                        name="category"
                        class="form-control @error('category') is-invalid @enderror"
                        required>
                    <option value="">— Select Category —</option>
                    <option value="electronics" {{ $selectedCategory == 'electronics' ? 'selected' : '' }}>
                        🖥️ Electronics
                    </option>
                    <option value="clothing" {{ $selectedCategory == 'clothing' ? 'selected' : '' }}>
                        👕 Clothing
                    </option>
                    <option value="books" {{ $selectedCategory == 'books' ? 'selected' : '' }}>
                        📚 Books
                    </option>
                    <option value="food" {{ $selectedCategory == 'food' ? 'selected' : '' }}>
                        🍕 Food & Beverages
                    </option>
                    <option value="sports" {{ $selectedCategory == 'sports' ? 'selected' : '' }}>
                        ⚽ Sports & Outdoors
                    </option>
                    <option value="home" {{ $selectedCategory == 'home' ? 'selected' : '' }}>
                        🏠 Home & Garden
                    </option>
                </select>
                @error('category')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- ACTIVE CHECKBOX --}}
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           class="form-check-input"
                           value="1"
                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom: 0;">
                        Active (visible to customers)
                    </label>
                </div>
            </div>

            {{-- SUBMIT BUTTONS --}}
            <div style="display: flex; gap: 12px; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">
                    💾 Update Product
                </button>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection
