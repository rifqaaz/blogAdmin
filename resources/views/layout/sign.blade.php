<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sign')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bs-bg: #f6f4f2;
            --bs-btn: #94ba7dff;
            --bs-lgrn: #d9e6d1;
            --bs-grn: #0a6114;
        }
        body {
            background-color: var(--bs-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .sign-container {
            max-width: 450px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--bs-grn);
        }
        .form-control:focus {
            border-color: var(--bs-grn);
            box-shadow: 0 0 0 0.25rem #a8e6afff;
        }
        .bi {
            color: var(--bs-grn);
        }
        .btn-sign {
            background-color: var(--bs-btn);
            border: none;
            padding: 10px 0;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-sign:hover {
            background-color: var(--bs-grn);
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--bs-grn);
        }
        .input-group {
            position: relative;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .auto-dismiss {
            animation: fadeOut 1s ease-in 3s forwards;
        }
        @keyframes fadeOut {
            to { opacity: 0; display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                field.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>