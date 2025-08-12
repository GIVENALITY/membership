<!-- Navbar -->
<nav
  class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base ri ri-menu-line icon-md"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="icon-base ri ri-search-line icon-lg lh-0"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none"
          placeholder="{{ __('app.search') }}..."
          aria-label="{{ __('app.search') }}..." />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <!-- Language Switcher -->
      <li class="nav-item navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base ri ri-translate-2 icon-lg"></i>
          <span class="badge bg-primary rounded-pill badge-center h-px-20 w-px-20">{{ strtoupper(app()->getLocale()) }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto text-heading">{{ __('app.language') }}</h6>
            </div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">
              <div class="d-flex align-items-center">
                <i class="icon-base ri ri-flag-line me-2"></i>
                <span>{{ __('app.english') }}</span>
                @if(app()->getLocale() == 'en')
                  <i class="icon-base ri ri-check-line ms-auto text-primary"></i>
                @endif
              </div>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('language.switch', 'sw') }}">
              <div class="d-flex align-items-center">
                <i class="icon-base ri ri-flag-line me-2"></i>
                <span>{{ __('app.swahili') }}</span>
                @if(app()->getLocale() == 'sw')
                  <i class="icon-base ri ri-check-line ms-auto text-primary"></i>
                @endif
              </div>
            </a>
          </li>
          <!-- Debug info -->
          <li class="dropdown-divider"></li>
          <li>
            <div class="dropdown-item">
              <small class="text-muted">
                <strong>Locale Debug Info:</strong><br>
                Current: {{ app()->getLocale() }}<br>
                Session: {{ session('locale', 'none') }}<br>
                Config: {{ config('app.locale') }}<br>
                Request: {{ request()->getLocale() }}<br>
                App Facade: {{ App::getLocale() }}<br>
                <strong>Translation Test:</strong><br>
                Welcome: {{ __('app.welcome') }}<br>
                Dashboard: {{ __('app.dashboard') }}
              </small>
            </div>
          </li>
        </ul>
      </li>
      <!-- /Language Switcher -->

      <!-- Notifications -->
      <li class="nav-item navbar-dropdown dropdown-notifications dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base ri ri-notification-3-line icon-lg"></i>
          <span class="badge bg-danger rounded-pill badge-center h-px-20 w-px-20">4</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto text-heading">{{ __('app.notifications') }}</h6>
              <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read">
                <i class="ri ri-mail-open-line fs-4"></i>
              </a>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-unstyled dropdown-notifications-list pb-2">
              <li class="dropdown-notifications-item">
                <a href="javascript:void(0);" class="dropdown-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avatar avatar-online">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="msg-name">New message from <span class="msg-name-bold">Jane Doe</span></h6>
                      <p class="msg-time">1 min ago</p>
                      <p class="msg-time">Hello there! I'm wondering if you can help me with a problem I've been having.</p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top">
            <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center p-3">
              View all notifications
            </a>
          </li>
        </ul>
      </li>

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a
          class="nav-link dropdown-toggle hide-arrow p-0"
          href="javascript:void(0);"
          data-bs-toggle="dropdown">
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
            <a class="dropdown-item" href="{{ route('hotel.account') }}">
              <i class="icon-base ri ri-settings-4-line icon-md me-3"></i>
              <span>{{ __('app.settings') }}</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <div class="d-grid px-4 pt-2 pb-1">
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger d-flex w-100">
                  <small class="align-middle">{{ __('app.logout') }}</small>
                  <i class="ri ri-logout-box-r-line ms-2 ri-xs"></i>
                </button>
              </form>
            </div>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>
<!-- / Navbar --> 