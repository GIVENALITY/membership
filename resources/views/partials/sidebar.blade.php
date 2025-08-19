<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @if(auth()->user()->role !== 'superadmin' && auth()->user()->hotel && auth()->user()->hotel->logo_path)
          <img src="{{ auth()->user()->hotel->logo_url }}" alt="{{ auth()->user()->hotel->name }}" 
               style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @else
          <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--restaurant-primary-color) 0%, color-mix(in sRGB, var(--restaurant-primary-color) 70%, white) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <i class="ri ri-restaurant-line" style="font-size: 24px; color: white;"></i>
          </div>
        @endif
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">
        @if(auth()->user()->role === 'superadmin')
          {{ __('app.system_admin') }}
        @else
          <div style="line-height: 1.2;">
            <div style="font-size: 1.1rem;">{{ auth()->user()->hotel->name ?? 'Restaurant MS' }}</div>
            <small style="color: var(--restaurant-primary-color); font-weight: 500;">Management System</small>
          </div>
        @endif
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="icon-base ri ri-close-line align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  @if(auth()->user()->role === 'superadmin')
    @include('partials.sidebar-superadmin')
  @else
    @include('partials.sidebar-hotel')
  @endif
</aside> 