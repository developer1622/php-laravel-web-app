{{--
=============================================================================
LARAVEL BLADE LAYOUT TEMPLATE
=============================================================================

WHAT IS A LAYOUT?
-----------------
A layout is a "master template" that defines the common HTML structure
(header, navigation, footer) shared by all pages.

Individual pages "extend" this layout and fill in specific "sections"
(like content, title, etc.).

GO EQUIVALENT:
In Go's html/template, you'd use template composition:

  // layout.html
  <!DOCTYPE html>
  <html>
  <head><title>{{ template "title" . }}</title></head>
  <body>
    <nav>...</nav>
    {{ template "content" . }}
    <footer>...</footer>
  </body>
  </html>

  // Then in Go code:
  tmpl := template.Must(template.ParseFiles("layout.html", "page.html"))
  tmpl.ExecuteTemplate(w, "layout", data)

KEY DIFFERENCES:
- Laravel uses @yield('section') / @section('section') pattern.
- Go uses {{ template "name" . }} / {{ define "name" }} pattern.
- The concept is identical: define "holes" in the layout that child templates fill.

BLADE SYNTAX CHEATSHEET (for Go developers):
  {{ $variable }}         → Same as {{ .Variable }} in Go templates
  @if (condition)         → Same as {{ if .Condition }} in Go templates
  @foreach ($items as $item) → Same as {{ range .Items }} in Go templates
  @yield('section')       → Same as {{ template "section" . }} in Go
  @extends('layout')      → No direct Go equivalent (you parse files together)
  @section('name')        → Same as {{ define "name" }} in Go
  @csrf                   → No direct Go equivalent (you add CSRF tokens manually)

=============================================================================
--}}

<!DOCTYPE html>
<html lang="en">
<head>
    {{-- Meta tags --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{--
        @yield('title', 'Default Title') — This is a "placeholder".
        Child templates fill this with @section('title', 'My Page Title').
        If no child provides a title, 'Laravel Demo' is used as default.

        GO EQUIVALENT:
        <title>{{ template "title" . }}</title>

        But Go doesn't have default values for templates.
    --}}
    <title>@yield('title', 'Laravel Demo') — Laravel MVC for Go Developers</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{--
        We're using inline CSS here for simplicity.
        In a real Laravel app, you'd use Vite to compile CSS:
          @vite(['resources/css/app.css'])

        GO EQUIVALENT:
        In Go, you'd serve static files:
          mux.Handle("/static/", http.StripPrefix("/static/", http.FileServer(http.Dir("static"))))
        And link to them: <link rel="stylesheet" href="/static/css/app.css">
    --}}
    <style>
        /* ============================================ */
        /* CSS CUSTOM PROPERTIES (Design Tokens)       */
        /* ============================================ */
        :root {
            --bg-primary: #0f0f1a;
            --bg-secondary: #1a1a2e;
            --bg-card: #16213e;
            --bg-card-hover: #1a2742;
            --accent-primary: #6c63ff;
            --accent-secondary: #e94560;
            --accent-success: #00d2d3;
            --accent-warning: #feca57;
            --accent-danger: #ff6b6b;
            --text-primary: #f0f0f0;
            --text-secondary: #a0a0b0;
            --text-muted: #6b6b80;
            --border-color: #2a2a4a;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.4);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ============================================ */
        /* NAVIGATION BAR                              */
        /* ============================================ */
        .navbar {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-card) 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .navbar-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: white;
        }

        .navbar-brand-text {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-subtitle {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .navbar-nav {
            display: flex;
            gap: 8px;
            list-style: none;
        }

        .navbar-nav a {
            text-decoration: none;
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .navbar-nav a:hover,
        .navbar-nav a.active {
            background: rgba(108, 99, 255, 0.15);
            color: var(--accent-primary);
        }

        /* ============================================ */
        /* MAIN CONTENT AREA                           */
        /* ============================================ */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* ============================================ */
        /* FLASH MESSAGES (Success/Error alerts)       */
        /* ============================================ */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(0, 210, 211, 0.1);
            border: 1px solid rgba(0, 210, 211, 0.3);
            color: var(--accent-success);
        }

        .alert-danger {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: var(--accent-danger);
        }

        /* ============================================ */
        /* PAGE HEADER                                 */
        /* ============================================ */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* ============================================ */
        /* BUTTONS                                     */
        /* ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), #5a52d5);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 99, 255, 0.5);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-success), #00b3b4);
            color: var(--bg-primary);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 210, 211, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--accent-warning), #f0b429);
            color: var(--bg-primary);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-danger), #ee5a5a);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-outline:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
            background: rgba(108, 99, 255, 0.05);
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 0.8rem;
        }

        /* ============================================ */
        /* CARDS                                       */
        /* ============================================ */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            border-color: rgba(108, 99, 255, 0.3);
            box-shadow: var(--shadow-lg);
        }

        /* ============================================ */
        /* DATA TABLE                                  */
        /* ============================================ */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(108, 99, 255, 0.08);
        }

        th {
            text-align: left;
            padding: 14px 18px;
            font-weight: 600;
            color: var(--accent-primary);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(42, 42, 74, 0.5);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(108, 99, 255, 0.03);
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .price-tag {
            font-weight: 700;
            color: var(--accent-success);
            font-family: 'JetBrains Mono', monospace;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: rgba(0, 210, 211, 0.15);
            color: var(--accent-success);
        }

        .badge-inactive {
            background: rgba(255, 107, 107, 0.15);
            color: var(--accent-danger);
        }

        .badge-category {
            background: rgba(108, 99, 255, 0.12);
            color: var(--accent-primary);
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        /* ============================================ */
        /* FORMS                                       */
        /* ============================================ */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .form-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
            font-weight: 400;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6b80' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            accent-color: var(--accent-primary);
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Validation errors */
        .error-text {
            color: var(--accent-danger);
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .form-control.is-invalid {
            border-color: var(--accent-danger);
        }

        /* ============================================ */
        /* DETAIL PAGE                                 */
        /* ============================================ */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 1.25rem;
        }

        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .detail-value.price {
            color: var(--accent-success);
            font-size: 2rem;
        }

        /* ============================================ */
        /* EMPTY STATE                                 */
        /* ============================================ */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* ============================================ */
        /* FOOTER                                      */
        /* ============================================ */
        .footer {
            border-top: 1px solid var(--border-color);
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 4rem;
        }

        .footer a {
            color: var(--accent-primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .go-comparison {
            background: rgba(0, 173, 216, 0.06);
            border: 1px solid rgba(0, 173, 216, 0.2);
            border-radius: var(--radius-sm);
            padding: 1rem 1.25rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #7dd3e0;
            line-height: 1.7;
        }

        .go-comparison strong {
            color: #00d2d3;
        }

        /* ============================================ */
        /* RESPONSIVE DESIGN                           */
        /* ============================================ */
        @media (max-width: 768px) {
            .navbar-inner {
                flex-direction: column;
                gap: 12px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    {{-- ============================================== --}}
    {{-- NAVIGATION BAR                                 --}}
    {{-- ============================================== --}}
    <nav class="navbar">
        <div class="navbar-inner">
            {{--
                route('products.index') generates the URL for the named route.
                Named routes are like constants for URLs — if the URL changes,
                you only update it in the routes file, not everywhere.

                GO EQUIVALENT:
                There's no direct Go equivalent. In Go, you'd hardcode URLs:
                  <a href="/products">
                Or use a URL builder (less common in Go).
            --}}
            <a href="{{ route('products.index') }}" class="navbar-brand">
                <div class="navbar-brand-icon">L</div>
                <div>
                    <div class="navbar-brand-text">Laravel MVC Demo</div>
                    <div class="navbar-subtitle">For Go Developers — PHP/Laravel Explained</div>
                </div>
            </a>

            <ul class="navbar-nav">
                <li>
                    <a href="{{ route('products.index') }}" class="active">
                        📦 Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.create') }}">
                        ➕ Add New
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- ============================================== --}}
    {{-- MAIN CONTENT                                   --}}
    {{-- ============================================== --}}
    <main class="main-container">

        {{--
            SESSION FLASH MESSAGES
            ----------------------
            session('success') checks if there's a "success" flash message
            stored in the session.

            Flash messages are one-time messages that survive ONE redirect.
            They're set in the controller: redirect()->with('success', 'message')

            GO EQUIVALENT:
            In Go, you'd use a session package (like gorilla/sessions):

              // Setting (in handler):
              session.Flash(r, "success", "Product created!")

              // Reading (in template):
              {{ if .Flash.Success }}
                <div class="alert alert-success">{{ .Flash.Success }}</div>
              {{ end }}
        --}}
        @if (session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{--
            @yield('content') — This is THE MOST IMPORTANT line.
            This is where child templates inject their content.

            Each page (index, create, show, edit) will @extend this layout
            and @section('content') to fill this placeholder.

            GO EQUIVALENT:
            {{ template "content" . }}
        --}}
        @yield('content')

    </main>

    {{-- ============================================== --}}
    {{-- FOOTER                                         --}}
    {{-- ============================================== --}}
    <footer class="footer">
        <p>
            🚀 <strong>Laravel MVC Demo</strong> — Built for Go developers learning PHP/Laravel
        </p>
        <p style="margin-top: 8px;">
            Laravel v{{ app()->version() }} | PHP v{{ PHP_VERSION }}
        </p>
        <p style="margin-top: 4px;">
            Think of this like Go's <strong>net/http + html/template + GORM</strong>, but all in one framework.
        </p>
    </footer>

</body>
</html>
