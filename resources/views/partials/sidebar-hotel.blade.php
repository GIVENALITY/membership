<ul class="menu-inner py-1">
  <!-- Dashboard -->
  <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="menu-link">
      <i class="icon-base ri ri-dashboard-line menu-icon"></i>
      <div>{{ __('app.dashboard') }}</div>
    </a>
  </li>

  <!-- Members Management (All roles except cashier) -->
  @if(auth()->user()->role !== 'cashier')
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle" onclick="console.log('Members menu toggle clicked');">
      <i class="icon-base ri ri-team-line menu-icon"></i>
      <div>{{ __('app.members') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('members.index') ? 'active' : '' }}">
        <a href="{{ route('members.index') }}" class="menu-link">
          <div>{{ __('app.members_list') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('members.search-page') ? 'active' : '' }}">
        <a href="{{ route('members.search-page') }}" class="menu-link">
          <div>{{ __('app.search') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
        <a href="{{ route('members.create') }}" class="menu-link" onclick="console.log('Add Member clicked');">
          <div>{{ __('app.add_member') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('membership-types.*') ? 'active' : '' }}">
        <a href="{{ route('membership-types.index') }}" class="menu-link">
          <div>{{ __('app.membership_types') }}</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  <!-- QuickView (Admin, Manager, Cashier only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager', 'cashier']))
  <li class="menu-item {{ request()->routeIs('quickview.index') ? 'active' : '' }}">
    <a href="{{ route('quickview.index') }}" class="menu-link">
      <i class="icon-base ri ri-eye-line menu-icon"></i>
      <div>QuickView</div>
    </a>
  </li>
  @endif

  <!-- Dining Management (All roles) -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-restaurant-line menu-icon"></i>
      <div>{{ __('app.dining_management') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('dining.index') ? 'active' : '' }}">
        <a href="{{ route('dining.index') }}" class="menu-link">
          <div>{{ __('app.record_visit') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('dining.history') ? 'active' : '' }}">
        <a href="{{ route('dining.history') }}" class="menu-link">
          <div>{{ __('app.visit_history') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Birthday Notifications (All roles) -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-cake-line menu-icon"></i>
      <div>Birthday Notifications</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('birthdays.today') ? 'active' : '' }}">
        <a href="{{ route('birthdays.today') }}" class="menu-link">
          <div>Today's Birthdays</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('birthdays.this-week') ? 'active' : '' }}">
        <a href="{{ route('birthdays.this-week') }}" class="menu-link">
          <div>This Week's Birthdays</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Reports (Admin, Manager only) - Temporarily Hidden -->
  {{-- @if(in_array(auth()->user()->role, ['admin', 'manager']))
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
  @endif --}}

  <!-- Application Settings (Admin, Manager only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager']))
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-settings-3-line menu-icon"></i>
      <div>{{ __('app.application_settings') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
          <div>{{ __('app.general_settings') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('settings.points') ? 'active' : '' }}">
        <a href="{{ route('settings.points') }}" class="menu-link">
          <div>{{ __('app.points_system') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('settings.email') ? 'active' : '' }}">
        <a href="{{ route('settings.email') }}" class="menu-link">
          <div>{{ __('app.email_templates') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('settings.discounts') ? 'active' : '' }}">
        <a href="{{ route('settings.discounts') }}" class="menu-link">
          <div>{{ __('app.discount_rules') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('discounts.index') ? 'active' : '' }}">
        <a href="{{ route('discounts.index') }}" class="menu-link">
          <div>{{ __('app.discounts') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
        <a href="{{ route('alerts.index') }}" class="menu-link">
          <div>Alerts</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  <!-- Hotel Management (Admin, Manager only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager']))
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-restaurant-line menu-icon"></i>
      <div>{{ __('app.restaurant') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('hotel.profile') ? 'active' : '' }}">
        <a href="{{ route('hotel.profile') }}" class="menu-link">
          <div>{{ __('app.restaurant_profile') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('hotel.account') ? 'active' : '' }}">
        <a href="{{ route('hotel.account') }}" class="menu-link">
          <div>{{ __('app.settings') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('restaurant.settings') ? 'active' : '' }}">
        <a href="{{ route('restaurant.settings') }}" class="menu-link">
          <div>{{ __('app.restaurant_settings') }}</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  <!-- User Management (Admin, Manager only) -->
  @if(in_array(auth()->user()->role, ['admin', 'manager']))
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-user-settings-line menu-icon"></i>
      <div>{{ __('app.user_management') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('user-management.index') ? 'active' : '' }}">
        <a href="{{ route('user-management.index') }}" class="menu-link">
          <div>{{ __('app.users') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('user-management.create') ? 'active' : '' }}">
        <a href="{{ route('user-management.create') }}" class="menu-link">
          <div>{{ __('app.add_user') }}</div>
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