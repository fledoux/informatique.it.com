<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'App')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary mb-4">
    <div class="container">
      <a class="navbar-brand" href="{{ url('/') }}">App</a>
      <div class="ms-auto">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('company.index') }}">Companies</a>
      </div>
    </div>
  </nav>

  <main class="container">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>