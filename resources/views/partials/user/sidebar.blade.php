@php
    /*
     * Helper kecil: pakai route bernama kalau ada, kalau belum ada jatuh ke URL biasa
     * supaya sidebar tidak error "Route not defined" saat modul belum dibuat.
     * Sesuaikan nama route / path di $menu di bawah dengan route.php kamu.
     */
    $link = fn (string $name, string $path) => \Illuminate\Support\Facades\Route::has($name)
        ? route($name)
        : url($path);

    $authUser   = auth()->user();
    $profilNama = $authUser?->admin?->nama ?? $authUser?->username ?? 'Profil';

    // [label, icon FA6, url, pola route untuk state "active"]
    $menu = [
        ['Dashboard',            'fas fa-house',          $link('admin.dashboard', 'admin/dashboard'),                 ['admin.dashboard']],
        ['Kategori',             'fas fa-tag',            $link('admin.kategori.index', 'admin/kategori'),             ['admin.kategori*']],
        ['Tingkat HSK',          'fas fa-layer-group',    $link('admin.tingkat-hsk.index', 'admin/tingkat-hsk'),       ['admin.tingkat-hsk*']],
        ['Kosakata',             'fas fa-font',           $link('admin.kosakata.index', 'admin/kosakata'),             ['admin.kosakata*']],
        ['Grup & Soal Latihan',  'fas fa-square-check',   $link('admin.grup-soal.index', 'admin/grup-soal'),           ['admin.grup-soal*', 'admin.soal*']],
        ['Paragraf',             'fas fa-book-open',      $link('admin.paragraf.index', 'admin/paragraf'),             ['admin.paragraf*']],
        ['Pelajar',              'fas fa-users',          $link('admin.pelajar.index', 'admin/pelajar'),               ['admin.pelajar*']],
        ['Pengaturan Website',   'fas fa-gear',           $link('admin.pengaturan-website.edit', 'admin/pengaturan-website'), ['admin.pengaturan-website*']],
        [$profilNama,            'fas fa-circle-user',    $link('admin.profil', 'admin/profil'),                       ['admin.profil*']],
    ];
@endphp

<!-- Sidebar (khusus admin) -->
<div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            {{-- accessor logo_url supaya path storage/ selalu benar --}}
            <img src="{{ $settings->logo_url }}" alt="Logo" class="navbar-brand" width="66" height="66"
                onerror="this.onerror=null;this.src='{{ asset('default-image/default-logo.png') }}';">
            <span class="brand-text">
                <span class="brand-name">{{ $settings->nama ?? 'Nama Website' }}</span>
                {{-- Teks kecil di bawah nama: ganti sesuai selera --}}
                <span class="brand-sub">中文学习</span>
            </span>
        </a>

        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
            </button>
        </div>
        <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
        </button>
    </div>
    <!-- End Logo Header -->
</div>

<div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
        <ul class="nav nav-secondary">

            @foreach ($menu as [$label, $icon, $url, $patterns])
                <li class="nav-item {{ request()->routeIs(...$patterns) ? 'active' : '' }}">
                    <a href="{{ $url }}">
                        <i class="{{ $icon }}"></i>
                        <p>{{ $label }}</p>
                    </a>
                </li>
            @endforeach

            <!-- Logout -->
            <li class="nav-item">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-right-from-bracket"></i>
                    <p>Logout</p>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>
    </div>
</div>
