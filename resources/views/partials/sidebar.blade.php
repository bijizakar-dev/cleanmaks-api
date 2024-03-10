<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html">FG HRIS</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="index.html">FG</a>
      </div>
      <ul class="sidebar-menu">
          {{-- Dashboard Menu --}}
          <li class="menu-header">Dashboard</li>
          <li><a class="nav-link" href="{{ url('/')}}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>

          <li class="menu-header">Menu</li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Masterdata</span></a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="{{ url('employees')}}">Pegawai</a></li>
              <li><a class="nav-link" href="layout-transparent.html">User</a></li>
              <li><a class="nav-link" href="layout-top-navigation.html">Roles</a></li>
              <li><a class="nav-link" href="{{ url('divisi')}}">Divisi</a></li>
              <li><a class="nav-link" href="layout-top-navigation.html">Jabatan</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-th-large"></i> <span>Layanan</span></a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="layout-default.html">Absensi / Presensi</a></li>
              <li><a class="nav-link" href="layout-transparent.html">Cuti</a></li>
              <li><a class="nav-link" href="layout-top-navigation.html">Izin</a></li>
              <li><a class="nav-link" href="layout-top-navigation.html">Lembur</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Setting</span></a>
            <ul class="dropdown-menu">
              <li><a href="auth-forgot-password.html">Setting App</a></li>
              <li><a href="auth-login.html">Profile</a></li>
            </ul>
          </li>
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
          <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="fas fa-rocket"></i> Documentation
          </a>
        </div>
    </aside>
  </div>
