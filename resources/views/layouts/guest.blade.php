<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OrderCraft') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Additional Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
    :root {
    --primary-color: #3b82f6;
    --primary-dark: #2563eb;
    --primary-light: #93c5fd;
    --accent-color: #06b6d4;
}

body {
    font-family: 'Poppins', 'Figtree', sans-serif;
    background-color: #ffffff;
    color: #000000;
}

.bg-cool-gradient {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
    background-attachment: fixed;
}

.card-shadow {
    box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.05);
    transition: all 0.3s ease;
}

.login-card {
    border-radius: 12px;
    border-top: 4px solid var(--primary-color);
    background: #ffffff;
    backdrop-filter: blur(10px);
    width: 100%;
    max-width: 400px;
    color: #000000;
}

.logo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 1.5rem;
}

.logo {
    height: 60px;
    width: 60px;
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.2);
}

.logo svg {
    color: white;
    height: 32px;
    width: 32px;
}

.brand-text {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.subtitle {
    color: #475569;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-input {
    height: 42px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    width: 100%;
    padding: 0 12px;
    font-size: 0.95rem;
    transition: all 0.2s;
    background-color: #ffffff;
    color: #000000;
}

.form-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    outline: none;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
    margin-bottom: 0.375rem;
}

.login-btn {
    height: 42px;
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    color: white;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s;
    border: none;
    width: 100%;
    cursor: pointer;
}

.login-btn:hover {
    background: linear-gradient(45deg, var(--primary-dark), var(--primary-color));
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.form-group {
    margin-bottom: 1.25rem;
}

.footer-text {
    margin-top: 1.5rem;
    font-size: 0.75rem;
    color: #64748b;
    text-align: center;
}

.password-toggle-wrapper {
    position: relative;
}

.password-toggle-wrapper input {
    padding-right: 40px;
}

.password-toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle-btn:hover {
    color: var(--primary-color);
}

.password-toggle-btn svg {
    width: 20px;
    height: 20px;
}

    </style>
</head>

<body class="bg-cool-gradient">
    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        <div class="logo-container">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h1 class="brand-text">OrderCraft</h1>
            <p class="subtitle">Order Management System</p>
        </div>

        <div class="login-card card-shadow p-6">
            {{ $slot }}
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('password-toggle');
            const passwordInput = document.getElementById('password');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    // Toggle password visibility
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle icon
                    const eyeOpen = passwordToggle.querySelector('.eye-open');
                    const eyeClosed = passwordToggle.querySelector('.eye-closed');

                    if (type === 'password') {
                        eyeOpen.style.display = 'block';
                        eyeClosed.style.display = 'none';
                    } else {
                        eyeOpen.style.display = 'none';
                        eyeClosed.style.display = 'block';
                    }
                });
            }
        });
    </script>
</body>

</html>