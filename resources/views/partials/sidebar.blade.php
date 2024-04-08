<?php
    $menus = [
        [
            'name' => 'Dashboard',
            'url' => '/',
            'icon' => 'fas fa-fire',
        ],
        [
            'name' => 'Masterdata',
            'url' => '#',
            'icon' => 'fas fa-columns',
            'submenu' => [
                ['name' => 'Pegawai', 'url' => 'employees'],
                ['name' => 'User', 'url' => 'user'],
                ['name' => 'Divisi', 'url' => 'divisi'],
                ['name' => 'Jabatan', 'url' => 'jabatan'],
                ['name' => 'Tipe Jenis', 'url' => 'jenis-type'],
                ['name' => 'Jadwal Pegawai', 'url' => 'jadwal-shift'],
                ['name' => 'Hari Libur', 'url' => 'hari-libur'],
            ],
        ],
        [
            'name' => 'Layanan',
            'url' => '#',
            'icon' => 'fas fa-th-large',
            'submenu' => [
                ['name' => 'Absensi / Presensi', 'url' => 'absen'],
                ['name' => 'Cuti', 'url' => 'cuti'],
                ['name' => 'Izin', 'url' => 'izin'],
                ['name' => 'Lembur', 'url' => 'lembur']
            ],
        ],
        [
            'name' => 'Pengaturan',
            'url' => '#',
            'icon' => 'far fa-user',
            'submenu' => [
                ['name' => 'Setting App', 'url' => 'setting-app'],
                ['name' => 'Profile', 'url' => 'setting-profile']
            ],
        ],
    ];
?>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html">FG HRIS</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="index.html">FG</a>
      </div>
      <ul class="sidebar-menu">
        @if (!function_exists('is_submenu_active'))
            <?php
            function is_submenu_active($submenu)
            {
                foreach ($submenu as $item) {
                    if (request()->is($item['url'])) {
                        return true;
                    }
                }
                return false;
            }
            ?>
        @endif

        @foreach ($menus as $menu)
            @if (isset($menu['submenu']))
                <!-- Menu dengan submenu -->
                <li class="nav-item dropdown {{ is_submenu_active($menu['submenu']) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="{{ $menu['icon'] }}"></i>
                        <span>{{ $menu['name'] }}</span>
                    </a>
                    <ul class="dropdown-menu" style="{{ is_submenu_active($menu['submenu']) ? 'display:block' : '' }}">
                        @foreach ($menu['submenu'] as $submenu)
                            <li class="{{ request()->is($submenu['url']) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url($submenu['url']) }}">
                                    {{ $submenu['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                <!-- Menu tanpa submenu -->
                <li class="nav-item {{ request()->is($menu['url']) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url($menu['url']) }}">
                        <i class="{{ $menu['icon'] }}"></i>
                        <span>{{ $menu['name'] }}</span>
                    </a>
                </li>
            @endif
        @endforeach
      </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
          <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="fas fa-rocket"></i> Documentation
          </a>
        </div>
    </aside>
  </div>
