<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item navbar-search-wrapper mb-0">
        <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
          <i class="icon-base ri ri-search-line icon-lg"></i>
          <span class="d-none d-md-inline-block text-muted ms-2">{{ __('app.search') }}...</span>
        </a>
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <!-- Simple Language Switcher -->
      <li class="nav-item me-3">
        <a href="{{ route('language.switch', 'en') }}" class="btn btn-sm btn-outline-primary">EN</a>
      </li>
      <li class="nav-item me-3">
        <a href="{{ route('language.switch', 'sw') }}" class="btn btn-sm btn-outline-primary">SW</a>
      </li>

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ Auth::user()->avatar_url }}" alt="alt" class="rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('users.profile') }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ Auth::user()->avatar_url }}" alt="alt" class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0">{{ Auth::user()->name ?? 'User' }}</h6>
                  <small class="text-body-secondary">{{ ucfirst(Auth::user()->role ?? 'User') }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('users.profile') }}">
              <i class="icon-base ri ri-user-line icon-md me-3"></i>
              <span>{{ __('app.profile') }}</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('users.change-password') }}">
              <i class="icon-base ri ri-lock-line icon-md me-3"></i>
              <span>{{ __('app.change_password') }}</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="icon-base ri ri-logout-box-line icon-md me-3"></i>
                <span>{{ __('app.logout') }}</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>
<!-- / Navbar --> 