<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('velzon/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('velzon/assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('velzon/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('velzon/assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                
                {{-- Dashboard --}}
                <li class="menu-title"><span data-key="t-menu">MENU</span></li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> 
                        <span data-key="t-dashboards">Dashboard</span>
                    </a>
                </li>

                {{-- ADMIN --}}
                @if(auth()->user()->role->name === 'admin')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-menu">Administración</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" 
                           href="{{ route('admin.usuarios.index') }}">
                            <i class="ri-user-line"></i> 
                            <span data-key="t-usuarios">Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.universidades.*') ? 'active' : '' }}" 
                           href="{{ route('admin.universidades.index') }}">
                            <i class="ri-building-line"></i> 
                            <span data-key="t-universidades">Universidades</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.planes.*') ? 'active' : '' }}" 
                           href="{{ route('admin.planes.index') }}">
                            <i class="ri-book-line"></i> 
                            <span data-key="t-planes">Planes de Estudio</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.revision.*') ? 'active' : '' }}" 
                           href="{{ route('admin.revision.index') }}">
                            <i class="ri-search-line"></i> 
                            <span data-key="t-revision">Revisión</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" 
                           href="{{ route('admin.reportes.index') }}">
                            <i class="ri-bar-chart-line"></i> 
                            <span data-key="t-reportes">Reportes</span>
                        </a>
                    </li>
                @endif

                {{-- UNIVERSIDAD --}}
                @if(auth()->user()->role->name === 'universidad')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-menu">Universidad</span></li>

                    

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('universidad.planes.*') ? 'active' : '' }}" 
                           href="{{ route('universidad.planes.index') }}">
                            <i class="ri-book-line"></i> 
                            <span data-key="t-planes">Planes de Estudio</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('universidad.reportes.*') ? 'active' : '' }}" 
                           href="{{ route('universidad.reportes.index') }}">
                            <i class="ri-bar-chart-line"></i> 
                            <span data-key="t-reportes">Mis Reportes</span>
                        </a>
                    </li>
                @endif

                {{-- EVALUADOR --}}
                @if(auth()->user()->role->name === 'evaluador')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-menu">Evaluador</span></li>

                

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('evaluador.revision.*') ? 'active' : '' }}" 
                           href="{{ route('evaluador.revision.index') }}">
                            <i class="ri-file-list-line"></i> 
                            <span data-key="t-revisiones">Revisiones</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('evaluador.reportes.*') ? 'active' : '' }}" 
                           href="{{ route('evaluador.reportes.index') }}">
                            <i class="ri-bar-chart-line"></i> 
                            <span data-key="t-reportes">Mis Reportes</span>
                        </a>
                    </li>
                @endif

                {{-- MESCYT --}}
                @if(auth()->user()->role->name === 'mescyt')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-menu">MESCyT</span></li>


                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('evaluaciones.index.*') ? 'active' : '' }}" 
                           href="{{ route('mescyt.evaluaciones.index') }}">
                            <i class="ri-file-text-line"></i> 
                            <span data-key="t-evaluacion">Evaluación</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('mescyt.reportes.*') ? 'active' : '' }}" 
                           href="{{ route('mescyt.reportes.index') }}">
                            <i class="ri-bar-chart-line"></i> 
                            <span data-key="t-reportes">Reportes</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->