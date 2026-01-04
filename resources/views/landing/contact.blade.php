<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - {{ $settings->clinic_name ?? 'Klinik ZIP' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-2xl font-bold text-blue-600">{{ $settings->clinic_name ?? 'Klinik ZIP' }}</a>
                <div class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-700 hover:text-blue-600">Home</a>
                    <a href="/services" class="text-gray-700 hover:text-blue-600">Layanan</a>
                    <a href="/doctors" class="text-gray-700 hover:text-blue-600">Dokter</a>
                    <a href="/about" class="text-gray-700 hover:text-blue-600">Tentang</a>
                    <a href="/contact" class="text-blue-600 font-semibold">Kontak</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Hubungi Kami</h1>
            <p class="text-xl text-blue-100">Kami siap membantu Anda</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Contact Info -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Informasi Kontak</h2>
                
                @if($settings && $settings->clinic_address)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Alamat</h3>
                    <p class="text-gray-600">{{ $settings->clinic_address }}</p>
                </div>
                @endif

                @if($settings && $settings->clinic_phone)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Telepon</h3>
                    <p class="text-gray-600">{{ $settings->clinic_phone }}</p>
                </div>
                @endif

                @if($settings && $settings->clinic_email)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                    <p class="text-gray-600">{{ $settings->clinic_email }}</p>
                </div>
                @endif

                @if($settings && $settings->clinic_whatsapp)
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-1">WhatsApp</h3>
                    <p class="text-gray-600">{{ $settings->clinic_whatsapp }}</p>
                </div>
                @endif
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                
                @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
                @endif

                <form action="/contact/submit" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea name="message" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 text-gray-300 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} {{ $settings->clinic_name ?? 'Klinik ZIP' }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>