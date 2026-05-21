<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <!-- Logo y Toggle del Sidebar -->
  <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route(Auth::user()->rol === 'Admin' ? 'home' : 'user.home') }}" class="logo d-flex align-items-center">
      <img src="{{ asset('NiceAdmin/assets/img/logo.png') }}" alt="IoT Logo">
      <span class="d-none d-lg-block">IoT Project</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>


  <!-- Navegación de Iconos y Perfil -->
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <!-- Menú Desplegable del Perfil -->
      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="{{ Auth::user()->imagen_url ? asset('storage/' . Auth::user()->imagen_url) : asset('NiceAdmin/assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
          <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6>{{ Auth::user()->name }} - {{ Auth::user()->rol }}</h6>
            <span>{{ Auth::user()->email }}</span>
          </li>
          
          <li><hr class="dropdown-divider"></li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('perfil.index') }}">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          
          <li><hr class="dropdown-divider"></li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('pendiente.index') }}">
              <i class="bi bi-gear"></i>
              <span>Account Settings</span>
            </a>
          </li>
          
          <li><hr class="dropdown-divider"></li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}">
              <i class="bi bi-box-arrow-right"></i>
              <span>Salir</span>
            </a>
          </li>

        </ul><!-- Fin Dropdown Items -->
      </li><!-- Fin Profile Nav -->

    </ul>
  </nav><!-- Fin Icons Navigation -->

</header><!-- Fin Header -->