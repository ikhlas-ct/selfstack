<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Explore ethical and responsible Mentawai cultural tourism. Discover Mentawai tribe traditions, customs, and authentic experiences on Mentawai Island, Indonesia. Sustainable tourism for ethnic culture enthusiasts.">
    <meta name="keywords"
        content="Mentawai tourism, Mentawai culture, Mentawai tribe, Mentawai traditions, Mentawai Island, Indonesia tourism, Mentawai ethnic, Mentawai customs, ethical cultural tourism, responsible tourism">
    <meta name="author" content="Mentawai Tribe">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Mentawai Tribe - Ethical and Responsible Mentawai Cultural Tourism')">
    <meta property="og:description"
        content="Explore ethical and responsible Mentawai cultural tourism. Discover Mentawai tribe traditions, customs, and authentic experiences on Mentawai Island, Indonesia.">
    <meta property="og:image" content="{{ asset($settings->logo ?? 'default-image/default-logo.png') }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="website">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Mentawai Tribe - Ethical and Responsible Mentawai Cultural Tourism')">
    <meta name="twitter:description"
        content="Explore ethical and responsible Mentawai cultural tourism. Discover Mentawai tribe traditions, customs, and authentic experiences on Mentawai Island, Indonesia.">
    <meta name="twitter:image" content="{{ asset($settings->logo ?? 'default-image/default-logo.png') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ request()->url() }}">
    <link rel="icon" href="{{ asset($settings->logo ?? 'default-image/default-logo.png') }}" type="image/x-icon">

    <title>
        @hasSection('title')
            @yield('title') - Mentawai Tribe: Ethical Cultural Tourism on Mentawai Island
        @else
            Mentawai Tribe: Ethical and Responsible Cultural Tourism on Mentawai Island, Indonesia
        @endif
    </title>

    <!-- Fonts and Icons -->
    <script src="{{ asset('user/js/plugin/webfont/webfont.min.js') }}"></script>
    <link href="{{ asset('home/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 6 Free",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('user/css/all.min.css') }}"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('user/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/kaiadmin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/override.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/red-theme.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    @yield('styles')
    <style>

    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-background-color="dark">
            @include('partials.user.sidebar')
        </div>

        <div class="main-panel">
            <div class="main-header">
                @include('partials.user.header')
            </div>

            {{-- ===== PAGE HEADER OTOMATIS (judul + ikon + breadcrumb dari nama route) =====
                 Timpa per halaman bila perlu: @section('page_title'), @section('page_icon'),
                 @section('hide_page_header', '1') untuk menyembunyikannya. --}}
            @php
                // pola route => [judul, ikon FA6]   (urutkan dari yang paling spesifik)
                $headers = [
                    'admin.dashboard'  => ['Dashboard',       'fas fa-tachometer-alt'],
                    'admin.kosakata*'  => ['Kosakata',        'fas fa-language'],
                    'admin.hafalan*'   => ['Progres Hafalan', 'fas fa-chart-pie'],
                    'admin.flashcard*' => ['Flashcard',       'fas fa-layer-group'],
                    'admin.grup-soal*' => ['Paket Soal',      'fas fa-clipboard-list'],
                    'admin.soal*'      => ['Paket Soal',      'fas fa-clipboard-list'],
                    'admin.kerjakan*'  => ['Kerjakan Soal',   'fas fa-pencil-alt'],
                    'admin.profil*'    => ['Profil Saya',     'fas fa-user-circle'],
                ];

                // akhiran route => label breadcrumb halaman terakhir
                $actions = ['create' => 'Tambah', 'edit' => 'Ubah', 'show' => 'Detail', 'import' => 'Import'];

                $title = trim($__env->yieldContent('page_title')) ?: null;
                $icon  = trim($__env->yieldContent('page_icon')) ?: null;

                foreach ($headers as $pattern => [$t, $i]) {
                    if (request()->routeIs($pattern)) {
                        $title ??= $t;
                        $icon  ??= $i;
                        break;
                    }
                }

                // breadcrumb
                $routeName = request()->route()?->getName() ?? '';
                $parts     = explode('.', $routeName);
                $last      = array_pop($parts);
                $base      = implode('.', $parts);
                $isDash    = request()->routeIs('admin.dashboard');

                $crumbs = ['Dashboard' => $isDash ? null : (\Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('admin/dashboard'))];

                if (! $isDash && $title) {
                    if (isset($actions[$last]) && \Illuminate\Support\Facades\Route::has($base . '.index')) {
                        $crumbs[$title]          = route($base . '.index');   // judul jadi link ke daftar
                        $crumbs[$actions[$last]] = null;                      // halaman aktif
                    } else {
                        $crumbs[$title] = null;
                    }
                }

                $hide = $__env->hasSection('hide_page_header') || ! $title;
            @endphp

            @unless ($hide)
                <div class="container cg-page-header-wrap">
                    <div class="page-inner pb-0">
                        <div class="page-header-card">
                            <div class="phc-icon"><i class="{{ $icon ?? 'fas fa-gear' }}"></i></div>
                            <div>
                                <h1 class="phc-title">{{ $title }}</h1>
                                <ol class="phc-breadcrumb">
                                    @foreach ($crumbs as $label => $url)
                                        <li>
                                            @if ($url)
                                                <a href="{{ $url }}">{{ $label }}</a>
                                            @else
                                                {{ $label }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @endunless

            @yield('content')

            <footer class="footer">
                @include('partials.user.footer')
            </footer>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('user/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('user/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('user/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/summernote/summernote-lite.min.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('user/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/chart-circle/circles.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('user/js/plugin/jsvectormap/world.js') }}"></script>
    <script src="{{ asset('user/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('user/js/kaiadmin.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Alert flash (class .alert-flash) hilang otomatis setelah 3 detik.
        // Tombol X (data-bs-dismiss="alert") tetap bisa dipakai kapan saja.
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.alert-flash').forEach(function (el) {
                setTimeout(function () {
                    if (!el.isConnected) return;       // sudah ditutup manual
                    el.classList.remove('show');       // fade out
                    setTimeout(function () { el.remove(); }, 300);
                }, 3000);
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
