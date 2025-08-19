<ul class="menu-inner py-1">
  <!-- Dashboard -->
  <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="menu-link">
      <i class="icon-base ri ri-dashboard-line menu-icon"></i>
      <div>{{ __('app.dashboard') }}</div>
    </a>
  </li>

  <!-- Operations (All roles) -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-settings-4-line menu-icon"></i>
      <div>Operations</div>
    </a>
    <ul class="menu-sub">
      <!-- Members Management (All roles except cashier) -->
      @if(auth()->user()->role !== 'cashier')
        <li class="menu-item {{ request()->routeIs('members.index') ? 'active' : '' }}">
          <a href="{{ route('members.index') }}" class="menu-link">
            <i class="icon-base ri ri-team-line me-2"></i>
            <div>{{ __('app.members_list') }}</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
          <a href="{{ route('members.create') }}" class="menu-link">
            <i class="icon-base ri ri-user-add-line me-2"></i>
            <div>{{ __('app.add_member') }}</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('members.search-page') ? 'active' : '' }}">
          <a href="{{ route('members.search-page') }}" class="menu-link">
            <i class="icon-base ri ri-search-line me-2"></i>
            <div>{{ __('app.search') }}</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('membership-types.*') ? 'active' : '' }}">
          <a href="{{ route('membership-types.index') }}" class="menu-link">
            <i class="icon-base ri ri-vip-crown-line me-2"></i>
            <div>{{ __('app.membership_types') }}</div>
          </a>
        </li>
      @endif
      
      <!-- Dining Management (All roles) -->
      <li class="menu-item {{ request()->routeIs('dining.index') ? 'active' : '' }}">
        <a href="{{ route('dining.index') }}" class="menu-link">
          <i class="icon-base ri ri-restaurant-line me-2"></i>
          <div>{{ __('app.record_visit') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('dining.history') ? 'active' : '' }}">
        <a href="{{ route('dining.history') }}" class="menu-link">
          <i class="icon-base ri ri-time-line me-2"></i>
          <div>{{ __('app.visit_history') }}</div>
        </a>
      </li>
      
      <!-- Transactions (Admin, Manager, Cashier only) -->
      @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
        <li class="menu-item {{ request()->routeIs('cashier.index') ? 'active' : '' }}">
          <a href="{{ route('cashier.index') }}" class="menu-link">
            <i class="icon-base ri ri-exchange-line me-2"></i>
            <div>{{ __('app.cashier') }}</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('discounts.index') ? 'active' : '' }}">
          <a href="{{ route('discounts.index') }}" class="menu-link">
            <i class="icon-base ri ri-percent-line me-2"></i>
            <div>{{ __('app.discounts') }}</div>
          </a>
        </li>
      @endif
    </ul>
  </li>

  <!-- Reports (Admin, Manager only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager']))
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-file-chart-line menu-icon"></i>
      <div>{{ __('app.reports') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('reports.members') ? 'active' : '' }}">
        <a href="{{ route('reports.members') }}" class="menu-link">
          <div>{{ __('app.members_report') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('reports.dining') ? 'active' : '' }}">
        <a href="{{ route('reports.dining') }}" class="menu-link">
          <div>{{ __('app.dining_report') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('reports.discounts') ? 'active' : '' }}">
        <a href="{{ route('reports.discounts') }}" class="menu-link">
          <div>{{ __('app.discounts_report') }}</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  <!-- Management (Admin, Manager only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager']))
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-settings-3-line menu-icon"></i>
      <div>Management</div>
    </a>
    <ul class="menu-sub">
      <!-- Restaurant Settings -->
      <li class="menu-item {{ request()->routeIs('hotel.profile') ? 'active' : '' }}">
        <a href="{{ route('hotel.profile') }}" class="menu-link">
          <i class="icon-base ri ri-restaurant-line me-2"></i>
          <div>{{ __('app.restaurant_profile') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('restaurant.settings') ? 'active' : '' }}">
        <a href="{{ route('restaurant.settings') }}" class="menu-link">
          <i class="icon-base ri ri-settings-4-line me-2"></i>
          <div>{{ __('app.restaurant_settings') }}</div>
        </a>
      </li>
      
      <!-- User Management -->
      <li class="menu-item {{ request()->routeIs('user-management.index') ? 'active' : '' }}">
        <a href="{{ route('user-management.index') }}" class="menu-link">
          <i class="icon-base ri ri-user-settings-line me-2"></i>
          <div>{{ __('app.users') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('user-management.create') ? 'active' : '' }}">
        <a href="{{ route('user-management.create') }}" class="menu-link">
          <i class="icon-base ri ri-user-add-line me-2"></i>
          <div>{{ __('app.add_user') }}</div>
        </a>
      </li>
      
      <!-- System Settings -->
      <li class="menu-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
          <i class="icon-base ri ri-settings-line me-2"></i>
          <div>{{ __('app.general_settings') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('settings.points') ? 'active' : '' }}">
        <a href="{{ route('settings.points') }}" class="menu-link">
          <i class="icon-base ri ri-star-line me-2"></i>
          <div>{{ __('app.points_system') }}</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  <!-- Onboarding (All roles) -->
  <li class="menu-item {{ request()->routeIs('onboarding.index') ? 'active' : '' }}">
    <a href="{{ route('onboarding.index') }}" class="menu-link">
      <i class="icon-base ri ri-book-open-line menu-icon"></i>
      <div>{{ __('app.onboarding') }}</div>
    </a>
  </li>

  <!-- Stop Impersonating Button (only shown when impersonating) -->
  @if(session('impersonator_id') && auth()->check())
    <li class="menu-divider"></li>
    <li class="menu-item">
      <a href="{{ route('impersonate.stop') }}" class="menu-link">
        <i class="icon-base ri ri-logout-box-r-line menu-icon"></i>
        <div>Logout I.M</div>
      </a>
    </li>
  @endif
</ul> 