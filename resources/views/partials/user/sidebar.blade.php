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

            <!-- Dashboard -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- ================= DATA MASTER ================= -->
            <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">Data Master</h4>
            </li>

            <!-- Kosakata (aktif juga di halaman create, edit, show, dan import) -->
            <li class="nav-item {{ request()->routeIs('admin.kosakata*') ? 'active' : '' }}">
                <a href="{{ route('admin.kosakata.index') }}">
                    <i class="fas fa-language"></i>
                    <p>Kosakata</p>
                </a>
            </li>

            <!-- Grup Kosakata / buku pelajaran (aktif juga di halaman create, edit, dan show) -->
            <li class="nav-item {{ request()->routeIs('admin.grup-kosakata*') ? 'active' : '' }}">
                <a href="{{ route('admin.grup-kosakata.index') }}">
                    <i class="fas fa-book"></i>
                    <p>Grup Kosakata</p>
                </a>
            </li>

            <!-- Radikal (aktif juga di halaman create, edit, show, dan import) -->
            <li class="nav-item {{ request()->routeIs('admin.radikal*') ? 'active' : '' }}">
                <a href="{{ route('admin.radikal.index') }}">
                    <i class="fas fa-cubes"></i>
                    <p>Radikal</p>
                </a>
            </li>

            <!-- Paket Soal (aktif juga di halaman detail paket, tambah/ubah paket, dan tambah/ubah soal) -->
            <li class="nav-item {{ request()->routeIs('admin.grup-soal*', 'admin.soal*') ? 'active' : '' }}">
                <a href="{{ route('admin.grup-soal.index') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Paket Soal</p>
                </a>
            </li>

            <!-- ================= PROSES & PROGRES ================= -->
            <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">Proses &amp; Progres</h4>
            </li>

            <!-- Progres Hafalan -->
            {{-- 'admin.hafalan.*' (pakai titik) supaya tidak ikut aktif di admin.hafalan-radikal.* --}}
            <li class="nav-item {{ request()->routeIs('admin.hafalan.*') ? 'active' : '' }}">
                <a href="{{ route('admin.hafalan.index') }}">
                    <i class="fas fa-chart-pie"></i>
                    <p>Progres Hafalan</p>
                </a>
            </li>

            <!-- Progres Hafalan Radikal -->
            <li class="nav-item {{ request()->routeIs('admin.hafalan-radikal.*') ? 'active' : '' }}">
                <a href="{{ route('admin.hafalan-radikal.index') }}">
                    <i class="fas fa-puzzle-piece"></i>
                    <p>Progres Radikal</p>
                </a>
            </li>

            <!-- Flashcard Hafalan -->
            <li class="nav-item {{ request()->routeIs('admin.flashcard*') ? 'active' : '' }}">
                <a href="{{ route('admin.flashcard.index') }}">
                    <i class="fas fa-layer-group"></i>
                    <p>Flashcard</p>
                </a>
            </li>

            <!-- Kerjakan Soal (aktif juga di halaman paket, mengerjakan soal, dan hasil) -->
            <li class="nav-item {{ request()->routeIs('admin.kerjakan*') ? 'active' : '' }}">
                <a href="{{ route('admin.kerjakan.index') }}">
                    <i class="fas fa-pencil-alt"></i>
                    <p>Kerjakan Soal</p>
                </a>
            </li>

            <!-- ================= PENGATURAN ================= -->
            <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">Pengaturan</h4>
            </li>

            <!-- Pengaturan Website (nama, logo, kontak, halaman depan, target belajar) -->
            <li class="nav-item {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
                <a href="{{ route('admin.pengaturan.edit') }}">
                    <i class="fas fa-cog"></i>
                    <p>Pengaturan Website</p>
                </a>
            </li>

            <!-- Profil Saya (nama, moto, foto, username, email, password) -->
            <li class="nav-item {{ request()->routeIs('admin.profil*') ? 'active' : '' }}">
                <a href="{{ route('admin.profil') }}">
                    <i class="fas fa-user-circle"></i>
                    <p>Profil Saya</p>
                </a>
            </li>

            {{-- Menu berikutnya ditambah di sini setelah route-nya dibuat.
                 Menu data -> di bawah "Data Master"; menu belajar/progres -> di bawah "Proses & Progres"; menu pengaturan -> di bawah "Pengaturan". --}}

            <!-- Logout -->
            <li class="nav-item mt-4">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <p>Logout</p>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>
    </div>
</div>
