<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- SECCIÓN DE EQUIPOS / CATÁLOGO --}}
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-cpu"></i>
                <span>
                    @can('ver-admin') Catálogo de Hardware @else Mis Equipos @endcan
                </span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                @can('ver-admin')
                    <li>
                        <a href="{{ route('maestros_catalogo.index') }}">
                            <i class="bi bi-circle"></i><span>Catálogo Maestros</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('esclavos.catalogo.index') }}">
                            <i class="bi bi-circle"></i><span>Catálogo Esclavos</span>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('user.maestros.index') }}">
                            <i class="bi bi-circle"></i><span>Mis Maestros</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.esclavos.index') }}">
                            <i class="bi bi-circle"></i><span>Mis Esclavos</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>

        {{-- UBICACIONES: SOLO PARA EL USUARIO --}}
        @cannot('ver-admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.ubicaciones.index') }}">
                <i class="bi bi-geo-alt"></i>
                <span>Mis Ubicaciones</span>
            </a>
        </li>
        @endcannot

        {{-- SECCIÓN EXCLUSIVA DE ADMINISTRACIÓN --}}
        @can('ver-admin')
            <li class="nav-heading">Configuración Maestra</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Gestión de Usuarios</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('unidades.medida.index') }}">
                    <i class="bi bi-rulers"></i>
                    <span>Unidades de Medida</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('componentes.index') }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Catálogo de Componentes</span>
                </a>
            </li>
        @endcan

    </ul>
</aside>