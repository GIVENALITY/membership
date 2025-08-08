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
          placeholder="Search..."
          aria-label="Search..." />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <!-- Notifications -->
      <li class="nav-item navbar-dropdown dropdown-notifications dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="icon-base ri ri-notification-3-line icon-lg"></i>
          <span class="badge bg-danger rounded-pill badge-center h-px-20 w-px-20">4</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto text-heading">Notifications</h6>
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
            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="alt" class="rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt="alt" class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0">{{ Auth::user()->name ?? 'User' }}</h6>
                  <small class="text-body-secondary">{{ Auth::user()->email ?? 'user@example.com' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <i class="icon-base ri ri-user-line icon-md me-3"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('settings.index') }}">
              <i class="icon-base ri ri-settings-4-line icon-md me-3"></i>
              <span>Settings</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          <li>
            <div class="d-grid px-4 pt-2 pb-1">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger d-flex">
                  <small class="align-middle">Logout</small>
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