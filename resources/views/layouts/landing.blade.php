<!-- resources/views/layouts/landing.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Klinik ZIP - Orthodontic & Dental Specialist')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6B4423',
                        'primary-dark': '#5A3A1E',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white">
    <x-landing-navbar />
    
    @yield('content')
    
    <x-landing-footer />
</body>
</html>