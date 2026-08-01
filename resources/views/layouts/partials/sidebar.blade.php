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

                {{-- ADMIN (solo admin normal, no admin2) --}}
                @if(auth()->user()->role->name === 'admin' && auth()->user()->role_id != 4)
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-administration">Administración</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" 
                           href="{{ route('admin.usuarios.index') }}">
                            <i class="ri-user-line"></i> 
                            <span data-key="t-usuarios">Usuarios</span>
                        </a>
                    </li>
                @endif

                {{-- Sucursales - admin normal y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" 
                           href="{{ route('branches.index') }}">
                            <i class="ri-community-line"></i>
                            <span data-key="t-sucursales">Sucursales</span>
                        </a>
                    </li>
                @endif

                {{-- Seguros Médicos - admin normal y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('insurances.*') ? 'active' : '' }}" 
                           href="{{ route('insurances.index') }}">
                            <i class="ri-shield-cross-line"></i>
                            <span data-key="t-seguros">Seguros Médicos</span>
                        </a>
                    </li>
                @endif

                {{-- Menú Desplegable de Servicios - Solo admin (no role_id 4) --}}
@if(in_array(auth()->user()->role->name, ['admin']) && auth()->user()->role_id != 4)
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('service-categories.*', 'services.*', 'taxes.*') ? '' : 'collapsed' }}" 
           href="#sidebarServicios" 
           data-bs-toggle="collapse" 
           role="button" 
           aria-expanded="{{ request()->routeIs('service-categories.*', 'services.*', 'taxes.*') ? 'true' : 'false' }}" 
           aria-controls="sidebarServicios">
            <i class="ri-tools-line"></i> 
            <span data-key="t-servicios">Servicios</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('service-categories.*', 'services.*', 'taxes.*') ? 'show' : '' }}" id="sidebarServicios">
            <ul class="nav nav-sm flex-column">
                {{-- Submenú 1: Categorías --}}
                <li class="nav-item">
                    <a href="{{ route('service-categories.index') }}" 
                       class="nav-link {{ request()->routeIs('service-categories.*') ? 'active' : '' }}" 
                       data-key="t-categorias">
                        Categorías
                    </a>
                </li>
                {{-- Submenú 2: Estudios --}}
                <li class="nav-item">
                    <a href="{{ route('services.index') }}" 
                       class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" 
                       data-key="t-estudios">
                        Estudios
                    </a>
                </li>
                {{-- Submenú 3: Impuestos --}}
                <li class="nav-item">
                    <a href="{{ route('taxes.index') }}" 
                       class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}" 
                       data-key="t-impuestos">
                        Impuestos
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif

                {{-- Pacientes - admin, recepcionista y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin','recepcionista']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" 
                           href="{{ route('patients.index') }}">
                            <i class="ri-stethoscope-line"></i>
                            <span data-key="t-pacientes">Pacientes</span>
                        </a>
                    </li>
                @endif

                {{-- Facturación - admin, recepcionista y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin','recepcionista']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" 
                           href="{{ route('invoices.index') }}">
                            <i class="ri-bill-line"></i>
                            <span data-key="t-facturacion">Facturación</span>
                        </a>
                    </li>
                @endif

                {{-- Pagar - admin, recepcionista y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin','recepcionista']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}" 
                           href="{{ route('receipts.index') }}">
                            <i class="ri-bank-card-line"></i>
                            <span data-key="t-pagar">Pagar</span>
                        </a>
                    </li>
                @endif

                {{-- Citas - admin, recepcionista y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin','recepcionista']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" 
                           href="{{ route('appointments.index') }}">
                            <i class="ri-calendar-check-line"></i>
                            <span data-key="t-citas">Citas</span>
                        </a>
                    </li>
                @endif

                {{-- Citas para audiólogo --}}
                @if(in_array(auth()->user()->role->name, ['audiologo']))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('audiologist.appointments*') ? 'active' : '' }}" 
                           href="{{ route('audiologist.appointments.index') }}">
                            <i class="ri-calendar-check-line"></i>
                            <span data-key="t-citas-audiologo">Citas</span>
                        </a>
                    </li>
                @endif

                {{-- Honorarios - admin normal y admin2 --}}
                @if(in_array(auth()->user()->role->name, ['admin']) || auth()->user()->role_id == 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('audiologist-fees.*') ? '' : 'collapsed' }}" 
                           href="#sidebarAudiologistFees" 
                           data-bs-toggle="collapse" 
                           role="button" 
                           aria-expanded="{{ request()->routeIs('audiologist-fees.*') ? 'true' : 'false' }}" 
                           aria-controls="sidebarAudiologistFees">
                            <i class="ri-money-dollar-circle-line"></i> 
                            <span data-key="t-honorarios-parent">Honorarios</span>
                        </a>
                        
                        <div class="collapse {{ request()->routeIs('audiologist-fees.*') ? 'show' : '' }}" id="sidebarAudiologistFees">
                            <ul class="nav nav-sm flex-column">
                                
                                <li class="nav-item">
                                    <a href="{{ route('audiologist-fees.index') }}" 
                                       class="nav-link {{ request()->routeIs('audiologist-fees.index') ? 'active' : '' }}">
                                        <span data-key="t-fee-gestion">Gestión de Honorarios</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('audiologist-fees.payments') }}" 
                                       class="nav-link {{ request()->routeIs('audiologist-fees.payments*') ? 'active' : '' }}">
                                        <span data-key="t-fee-pagos">Pagos / Historial</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('audiologist-fees.settings') }}" 
                                       class="nav-link {{ request()->routeIs('audiologist-fees.settings*') ? 'active' : '' }}">
                                        <span data-key="t-fee-config">Configuraciones</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif

                {{-- Reportes Admin - solo admin normal (no admin2) --}}
                @if(in_array(auth()->user()->role->name, ['admin']) && auth()->user()->role_id != 4)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" 
                           href="{{ route('reports.index') }}">
                            <i class="ri-bar-chart-line"></i>
                            <span data-key="t-reportes-admin">Reportes</span>
                        </a>
                    </li>
                @endif

                {{-- Historia Clínica - solo audiólogo --}}
                @if(in_array(auth()->user()->role->name, ['audiologo']))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('clinical-records*') ? 'active' : '' }}" 
                           href="{{ route('clinical-records.index') }}">
                            <i class="ri-file-history-line"></i>
                            <span data-key="t-historia">Historia Clinica</span>
                        </a>
                    </li>
                @endif

                {{-- Reportes Recepcionista - solo recepcionista --}}
                @if(in_array(auth()->user()->role->name, ['recepcionista']))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('receptionist.reports.*') ? 'active' : '' }}" 
                           href="{{ route('receptionist.reports.index') }}">
                            <i class="ri-bar-chart-line"></i>
                            <span data-key="t-reportes-recep">Reportes</span>
                        </a>
                    </li>
                @endif
                       
                {{-- Reportes Audiólogo - solo audiólogo --}}
                @if(in_array(auth()->user()->role->name, ['audiologo']))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('audiologist.reports*') ? 'active' : '' }}" 
                           href="{{ route('audiologist.reports.index') }}">
                            <i class="ri-bar-chart-line"></i>
                            <span data-key="t-reportes-audio">Reportes</span>
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