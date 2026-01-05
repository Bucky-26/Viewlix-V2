<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Developer Portfolio')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #ffffff;
            --primary-dark: #f0f0f0;
            --secondary: #6366f1;
            --dark: #1f2937;
            --light: #f3f4f6;
            --border: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            background-color: #ffffff;
            line-height: 1.6;
        }

        /* Navigation */
        nav {
            background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav .brand:hover {
            opacity: 0.9;
        }

        nav .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
        }

        nav .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        nav .nav-links a:hover {
            opacity: 0.8;
        }

        nav .nav-links .btn-admin {
            background-color: var(--secondary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }

        nav .nav-links .btn-admin:hover {
            background-color: #db2777;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 3rem 2rem;
            margin-top: 5rem;
        }

        footer .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        footer h4 {
            margin-bottom: 1rem;
            color: var(--secondary);
        }

        footer a {
            color: #d1d5db;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        footer a:hover {
            color: white;
        }

        footer .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 2rem;
            text-align: center;
            color: #9ca3af;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--dark);
            border: 2px solid #e5e7eb;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4f46e5;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-outline {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            background-color: transparent;
        }

        .btn-outline:hover {
            background-color: var(--secondary);
            color: white;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-header {
            background-color: var(--light);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Typography */
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        p {
            margin-bottom: 1rem;
            color: #6b7280;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: var(--primary-dark);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .grid-4 {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #7f1d1d;
            border-color: #ef4444;
        }

        .alert-info {
            background-color: #e0f2fe;
            color: #0c2d4a;
            border-color: #0284c7;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination .active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav .nav-links {
                gap: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .container {
                padding: 0 1rem;
            }

            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }
        }

        /* Loading */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h3 {
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #9ca3af;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container" style="padding: 0;">
            <a href="/" class="brand">
                <span>🚀</span>
                <span>Viewlix</span>
            </a>
            <ul class="nav-links">
                <li><a href="/developer/">Portfolio</a></li>
                <li><a href="/developer/blogs">Blog</a></li>
                @auth
                    @if(auth()->user()->is_admin)
                        <li><a href="/developer/adminpane/" class="btn-admin">Admin Panel</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-small" style="background-color: rgba(255,255,255,0.2); color: white; border: none; cursor: pointer;">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="/login">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div>
                <h4>About</h4>
                <p>Welcome to my developer portfolio. Explore my work and latest blog posts.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <a href="/developer/">Portfolio</a>
                <a href="/developer/blogs">Blog</a>
                <a href="/developer/adminpane/">Admin</a>
            </div>
            <div>
                <h4>Connect</h4>
                <a href="#">Twitter</a>
                <a href="#">GitHub</a>
                <a href="#">LinkedIn</a>
            </div>
            <div>
                <h4>Contact</h4>
                <p>Email: hello@example.com</p>
                <p>Phone: +1 (555) 000-0000</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Developer Portfolio. All rights reserved.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
