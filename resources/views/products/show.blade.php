{{--
=============================================================================
PRODUCT SHOW VIEW — Display Single Product Details
=============================================================================

This view shows the details of a single product.
$product is passed from ProductController@show.

GO EQUIVALENT:
  // Handler fetches product by ID:
  product, err := repo.FindByID(id)
  // Then renders:
  tmpl.ExecuteTemplate(w, "products/show", map[string]interface{}{
      "product": product,
  })

=============================================================================
--}}

@extends('layouts.app')

{{--
    Passing dynamic values to yield:
    Here we use $product->name as the page title.

    GO EQUIVALENT:
    {{ define "title" }}{{ .Product.Name }}{{ end }}
--}}
@section('title', $product->name)

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">
                Product #{{ $product->id }} — Created {{ $product->created_at->diffForHumans() }}
            </p>
            <div class="go-comparison">
                <strong>🐹 Go equivalent:</strong> This is like a handler doing
                <code>db.First(&product, id)</code> then rendering the detail template.
                The <code>diffForHumans()</code> is like Go's
                <code>time.Since(product.CreatedAt)</code> with human-readable formatting.
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('products.index') }}" class="btn btn-outline">
                ← Back to List
            </a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                ✏️ Edit
            </a>
        </div>
    </div>

    {{-- ================================================== --}}
    {{-- PRODUCT DETAILS GRID                               --}}
    {{-- ================================================== --}}
    {{--
        We display product fields in a grid of cards.

        GO EQUIVALENT:
        In Go templates, you'd access fields like:
          {{ .Product.Name }}
          {{ .Product.Price }}
          {{ .Product.Category }}

        In Blade:
          {{ $product->name }}
          {{ $product->price }}
          {{ $product->category }}

        KEY DIFFERENCE:
        - Go: dot notation, no $ sign
        - PHP: arrow notation (->), with $ prefix for variables
    --}}
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">💰 Price</div>
            <div class="detail-value price">{{ $product->formatted_price }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">📦 Quantity in Stock</div>
            <div class="detail-value">{{ $product->quantity }} units</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">🏷️ Category</div>
            <div class="detail-value">
                <span class="badge badge-category">{{ ucfirst($product->category) }}</span>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">📊 Status</div>
            <div class="detail-value">
                @if ($product->is_active)
                    <span class="badge badge-active">✓ Active</span>
                @else
                    <span class="badge badge-inactive">✗ Inactive</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Description Card --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--accent-primary); margin-bottom: 0.75rem;">📝 Description</h3>
        {{--
            $product->description ?? 'No description provided.'
            The ?? is the "null coalescing" operator in PHP.
            If $product->description is null, use the fallback string.

            GO EQUIVALENT:
            Go doesn't have a null coalescing operator. You'd use:
              description := product.Description
              if description == nil || *description == "" {
                  description = stringPtr("No description provided.")
              }

            Or in templates:
              {{ if .Product.Description }}
                  {{ .Product.Description }}
              {{ else }}
                  No description provided.
              {{ end }}
        --}}
        <p style="color: var(--text-secondary); line-height: 1.8;">
            {{ $product->description ?? 'No description provided.' }}
        </p>
    </div>

    {{-- Metadata Card --}}
    <div class="card">
        <h3 style="color: var(--accent-primary); margin-bottom: 1rem;">🕐 Timestamps</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <div class="detail-label">Created At</div>
                {{--
                    $product->created_at->format('F j, Y \a\t g:i A')
                    This uses Carbon (Laravel's date library) to format dates.
                    Carbon is built into Eloquent models for all timestamp fields.

                    GO EQUIVALENT:
                    product.CreatedAt.Format("January 2, 2006 at 3:04 PM")

                    Note: Go uses the "reference time" Mon Jan 2 15:04:05 MST 2006
                    for date formatting (yes, that specific date!).
                    PHP uses letters like 'Y' (year), 'm' (month), 'd' (day).
                --}}
                <div style="color: var(--text-secondary);">
                    {{ $product->created_at->format('F j, Y \a\t g:i A') }}
                </div>
            </div>
            <div>
                <div class="detail-label">Last Updated</div>
                <div style="color: var(--text-secondary);">
                    {{ $product->updated_at->format('F j, Y \a\t g:i A') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display: flex; gap: 12px; margin-top: 2rem;">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
            ✏️ Edit Product
        </a>
        <form action="{{ route('products.destroy', $product) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete {{ $product->name }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                🗑️ Delete Product
            </button>
        </form>
    </div>

@endsection
