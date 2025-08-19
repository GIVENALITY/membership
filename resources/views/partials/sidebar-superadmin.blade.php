<ul class="menu-inner py-1">
  <!-- Stop Impersonating Button (only shown when impersonating) -->
  @if(session('impersonator_id') && auth()->check())
    <li class="menu-item">
      <a href="{{ route('impersonate.stop') }}" class="menu-link" style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; border-radius: 8px; margin: 10px 0;">
        <i class="icon-base ri ri-logout-box-r-line menu-icon" style="color: white;"></i>
        <div style="color: white; font-weight: 600;">Stop Impersonating</div>
      </a>
    </li>
    <li class="menu-divider"></li>
  @endif

  <!-- Superadmin Dashboard -->
  <li class="menu-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
    <a href="{{ route('superadmin.dashboard') }}" class="menu-link">
      <i class="icon-base ri ri-dashboard-line menu-icon"></i>
      <div>{{ __('app.superadmin_dashboard') }}</div>
    </a>
  </li>

  <!-- System Management -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-settings-4-line menu-icon"></i>
      <div>{{ __('app.system_management') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('superadmin.system-settings') ? 'active' : '' }}">
        <a href="{{ route('superadmin.system-settings') }}" class="menu-link">
          <div>{{ __('app.system_settings') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('superadmin.translations') ? 'active' : '' }}">
        <a href="{{ route('superadmin.translations') }}" class="menu-link">
          <div>{{ __('app.translation_management') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
        <a href="{{ route('superadmin.logs') }}" class="menu-link">
          <div>{{ __('app.system_logs') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Multi-Tenant Management -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-building-line menu-icon"></i>
      <div>{{ __('app.tenant_management') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->routeIs('superadmin.hotels') ? 'active' : '' }}">
        <a href="{{ route('superadmin.hotels') }}" class="menu-link">
          <div>{{ __('app.all_hotels') }}</div>
        </a>
      </li>
      <li class="menu-item {{ request()->routeIs('superadmin.users') ? 'active' : '' }}">
        <a href="{{ route('superadmin.users') }}" class="menu-link">
          <div>{{ __('app.all_users') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Analytics & Reports -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-bar-chart-line menu-icon"></i>
      <div>{{ __('app.system_analytics') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.usage_statistics') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.performance_metrics') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.system_health') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Security & Access -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-shield-check-line menu-icon"></i>
      <div>{{ __('app.security_management') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.access_control') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.audit_logs') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.security_settings') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Backup & Maintenance -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-tools-line menu-icon"></i>
      <div>{{ __('app.backup_maintenance') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.database_backup') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.system_maintenance') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.update_management') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Help & Support -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-customer-service-line menu-icon"></i>
      <div>{{ __('app.help_support') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.system_documentation') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.support_tickets') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.contact_support') }}</div>
        </a>
      </li>
    </ul>
  </li>

  <!-- Divider -->
  <li class="menu-divider"></li>

  <!-- Quick Actions -->
  <li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="icon-base ri ri-flashlight-line menu-icon"></i>
      <div>{{ __('app.quick_actions') }}</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.create_hotel') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.system_status') }}</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="#" class="menu-link">
          <div>{{ __('app.emergency_mode') }}</div>
        </a>
      </li>
    </ul>
  </li>
</ul> 