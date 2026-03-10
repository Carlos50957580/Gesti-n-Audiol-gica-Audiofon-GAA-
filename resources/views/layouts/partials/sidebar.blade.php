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


           @if(in_array(auth()->user()->role->name,['admin']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" 
        href="{{ route('branches.index') }}">
            <i class="ri-community-line"></i>
            <span>Sucursales</span>
        </a>
    </li>
@endif

        @if(in_array(auth()->user()->role->name,['admin']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('insurances.*') ? 'active' : '' }}" 
        href="{{ route('insurances.index') }}">
        <i class="ri-shield-cross-line"></i>
        <span>Seguros Médicos</span>
        </a>
    </li>
@endif


             @if(in_array(auth()->user()->role->name,['admin']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('services.*') ? 'active' : '' }}" 
        href="{{ route('services.index') }}">
            <i class="ri-tools-line"></i>
            <span>Servicios</span>
        </a>
    </li>
@endif


                   @if(in_array(auth()->user()->role->name,['admin','recepcionista']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" 
           href="{{ route('patients.index') }}">
            <i class="ri-stethoscope-line"></i>
            <span>Pacientes</span>
        </a>
    </li>
@endif

                
                
                    @if(in_array(auth()->user()->role->name,['admin','recepcionista']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" 
           href="{{ route('appointments.index') }}">
            <i class="ri-calendar-check-line"></i>
            <span>Citas</span>
        </a>
    </li>
@endif


                  
                    
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

               

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->