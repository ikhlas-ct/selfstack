    <div class="container-fluid d-flex justify-content-between">
        <nav class="pull-left">
            <ul class="nav">
                <li class="nav-item">
                    {{ date('Y') }}, made with <i class="fa fa-heart heart text-danger"></i>
                    by {{ filled($settings->nama) ? $settings->nama : '-' }}
                </li>
            </ul>
        </nav>

        {{-- Slogan dari Pengaturan Website (disembunyikan di layar kecil & kalau kosong) --}}
        @if (filled($settings->slogan))
            <div class="d-none d-md-block text-muted fst-italic">{{ $settings->slogan }}</div>
        @endif
    </div>
