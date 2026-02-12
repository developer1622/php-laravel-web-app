{{--
=============================================================================
PRODUCT CREATE VIEW — Form to Add a New Product
=============================================================================

This view renders a form for creating a new product.
When submitted, the form sends a POST request to ProductController@store.

GO EQUIVALENT:
  This is like rendering a create.html template that posts to /products.
  In Go, you'd have:
    <form action="/products" method="POST">
        <input type="hidden" name="csrf_token" value="{{ .CSRFToken }}">
        <input type="text" name="name" value="">
        ...
        <button type="submit">Create</button>
    </form>

=============================================================================
--}}

@extends('layouts.app')

@section('title', 'Create Product')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">➕ Create New Product</h1>
            <p class="page-subtitle">Fill in the details to add a new product to the inventory.</p>
            <div class="go-comparison">
                <strong>🐹 Go equivalent:</strong> This is like a handler that renders
                <code>templates/products/create.html</code> for GET requests,
                and processes <code>r.ParseForm()</code> for POST requests.
            </div>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline">
            ← Back to List
        </a>
    </div>

    <div class="card">
        {{--
            FORM ELEMENT
            ============
            action="{{ route('products.store') }}" — The URL to submit to (POST /products).
            method="POST" — The HTTP method.

            GO EQUIVALENT:
            <form action="/products" method="POST">

            KEY POINT: HTML forms only support GET and POST.
            For PUT/PATCH/DELETE, Laravel uses @method('PUT') — a hidden input.
        --}}
        <form action="{{ route('products.store') }}" method="POST">

            {{--
                @csrf — Cross-Site Request Forgery protection.
                This generates a hidden input with a security token:
                  <input type="hidden" name="_token" value="random-token-here">

                Laravel checks this token on every POST/PUT/DELETE request.
                If the token is missing or invalid, Laravel rejects the request (419 error).

                GO EQUIVALENT:
                Using gorilla/csrf middleware:
                  <input type="hidden" name="gorilla.csrf.Token" value="{{ .CSRFToken }}">

                Or manually:
                  token := generateCSRFToken()
                  storeInSession(token)
                  // In template:
                  <input type="hidden" name="csrf_token" value="{{ .CSRFToken }}">
                  // In handler:
                  if r.FormValue("csrf_token") != session.Get("csrf_token") {
                      http.Error(w, "Forbidden", 403)
                  }

                Laravel handles ALL of this automatically with just @csrf!
            --}}
            @csrf

            {{-- PRODUCT NAME --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    Product Name <span style="color: var(--accent-danger);">*</span>
                </label>
                {{--
                    value="{{ old('name') }}" — This is the "old input" helper.
                    If validation fails and the form is re-displayed, this
                    pre-fills the field with what the user previously typed.

                    GO EQUIVALENT:
                    You'd store form values in the session before redirecting back:
                      session.SetFlash(r, "old_name", r.FormValue("name"))
                    Then in the template:
                      <input value="{{ .OldInput.Name }}">

                    Laravel does this automatically when validation fails!
                --}}
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="e.g., MacBook Pro 16-inch"
                       required>
                {{--
                    @error('name') ... @enderror — Display validation error for 'name'.

                    If the 'name' field fails validation, this block renders.
                    $message contains the error text (e.g., "The name field is required.").

                    GO EQUIVALENT:
                    {{ if .Errors.Name }}
                        <span class="error">{{ .Errors.Name }}</span>
                    {{ end }}
                --}}
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
                {{--
                    Textarea uses old('description') between the tags (not in value attribute).

                    GO EQUIVALENT:
                    <textarea name="description">{{ .OldInput.Description }}</textarea>
                --}}
                <textarea id="description"
                          name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe the product features, specs, etc.">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- PRICE & QUANTITY (side by side using CSS Grid) --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">
                        Price ($) <span style="color: var(--accent-danger);">*</span>
                    </label>
                    <input type="number"
                           id="price"
                           name="price"
                           value="{{ old('price') }}"
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
                           value="{{ old('quantity', 0) }}"
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
                    SELECT DROPDOWN
                    old('category') pre-selects the previously chosen option.
                    The "selected" attribute is set conditionally.

                    GO EQUIVALENT:
                    <select name="category">
                        <option value="electronics" {{ if eq .OldInput.Category "electronics" }}selected{{ end }}>
                            Electronics
                        </option>
                        ...
                    </select>
                --}}
                <select id="category"
                        name="category"
                        class="form-control @error('category') is-invalid @enderror"
                        required>
                    <option value="">— Select Category —</option>
                    <option value="electronics" {{ old('category') == 'electronics' ? 'selected' : '' }}>
                        🖥️ Electronics
                    </option>
                    <option value="clothing" {{ old('category') == 'clothing' ? 'selected' : '' }}>
                        👕 Clothing
                    </option>
                    <option value="books" {{ old('category') == 'books' ? 'selected' : '' }}>
                        📚 Books
                    </option>
                    <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>
                        🍕 Food & Beverages
                    </option>
                    <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>
                        ⚽ Sports & Outdoors
                    </option>
                    <option value="home" {{ old('category') == 'home' ? 'selected' : '' }}>
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
                    {{--
                        Checkbox: old('is_active', true)
                        The second argument 'true' is the default value (checked by default).

                        GO EQUIVALENT:
                        <input type="checkbox" name="is_active"
                               {{ if or .OldInput.IsActive (not .HasOldInput) }}checked{{ end }}>
                    --}}
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           class="form-check-input"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom: 0;">
                        Active (visible to customers)
                    </label>
                </div>
            </div>

            {{-- SUBMIT BUTTONS --}}
            <div style="display: flex; gap: 12px; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">
                    ✅ Create Product
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection
