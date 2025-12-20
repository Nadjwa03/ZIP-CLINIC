<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Halaman Pasien</title>
</head>
<body class="p-6">
  <h1 class="text-2xl font-bold mb-4">Halaman Pasien</h1>

  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
      class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
      Logout
    </button>
  </form>
</body>
</html>
