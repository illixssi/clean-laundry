<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            @if(!isset($showButtons) || $showButtons)
            <li class="header--nav--item nav--item--mobile"> <a class="nav-link bilist--container" data-lte-toggle="sidebar" href="#"
                    role="button"> <i class="bi bi-list fs-1"></i> </a> </li>
            @endif
        </ul>
        <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
            <li class="dropdown user-menu">
                <span class="d-md-inline">
                    {{ Auth::user()->name ?? 'Guest' }}, {{ Auth::user()->role->role_name ?? 'User' }}
                </span>
            </li>
        </ul>
    </div> <!--end::Container-->
</nav> <!--end::Header-->