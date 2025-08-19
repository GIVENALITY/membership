<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @if(auth()->user()->role !== 'superadmin' && auth()->user()->hotel && auth()->user()->hotel->logo_path)
          <img src="{{ auth()->user()->hotel->logo_url }}" alt="{{ auth()->user()->hotel->name }}" 
               style="width: 40px; height: 40px; object-fit: contain; border-radius: 6px;">
        @else
          <div style="width: 40px; height: 40px; background: var(--restaurant-primary-color); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
            <i class="ri ri-restaurant-line" style="font-size: 20px; color: white;"></i>
          </div>
        @endif
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">
        @if(auth()->user()->role === 'superadmin')
          {{ __('app.system_admin') }}
        @else
          <div style="font-size: 1rem;">{{ auth()->user()->hotel->name ?? 'Restaurant MS' }}</div>
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