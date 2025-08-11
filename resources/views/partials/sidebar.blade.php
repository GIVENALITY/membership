<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo me-1">
        @if(Auth::check() && Auth::user()->hotel && Auth::user()->hotel->logo_path)
          <img src="{{ Auth::user()->hotel->logo_url }}" alt="{{ Auth::user()->hotel->name }}" 
               style="width: 30px; height: 30px; object-fit: contain;">
        @else
          <span class="text-primary" style="color: {{ Auth::user()->hotel->primary_color ?? '#000000' }} !important;">
            <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z"
                fill="currentColor" />
              <path
                opacity="0.077704"
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z"
                fill="black" />
              <path
                opacity="0.077704"
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M0 65.2656L60.4839 99.0795V119.859L0 65.2656Z"
                fill="black" />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z"
                fill="currentColor" />
              <path
                opacity="0.077704"
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M250 65.2656L189.516 99.8897V135.006L250 65.2656Z"
                fill="black" />
              <path
                opacity="0.077704"
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M250 65.2656L189.516 99.0497V120.886L250 65.2656Z"
                fill="black" />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                fill="currentColor" />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                fill="white"
                fill-opacity="0.15" />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                fill="currentColor" />
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                fill="white"
                fill-opacity="0.3" />
            </svg>
          </span>
        @endif
      </span>
      <span class="app-brand-text demo menu-text fw-semibold ms-2" 
            style="color: {{ Auth::user()->hotel->primary_color ?? '#000000' }} !important;">
        {{ Auth::user()->hotel->name ?? 'Membership MS' }}
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-inline-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-home-smile-line"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Onboarding Guide -->
    <li class="menu-item {{ request()->routeIs('onboarding.*') ? 'active' : '' }}">
      <a href="{{ route('onboarding.index') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-rocket-line"></i>
        <div data-i18n="Onboarding Guide">Onboarding Guide</div>
      </a>
    </li>

    <!-- Members -->
    <li class="menu-item {{ request()->routeIs('members.*') ? 'active' : '' }}">
      <a href="{{ route('members.index') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-user-star-line"></i>
        <div data-i18n="Members">Members</div>
      </a>
    </li>

    <!-- Search Members -->
    <li class="menu-item {{ request()->routeIs('members.search') ? 'active' : '' }}">
      <a href="{{ route('members.search') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-search-line"></i>
        <div data-i18n="Search Members">Search Members</div>
      </a>
    </li>

               <!-- Add Member -->
           <li class="menu-item {{ request()->routeIs('members.create') ? 'active' : '' }}">
             <a href="{{ route('members.create') }}" class="menu-link">
               <i class="menu-icon icon-base ri ri-user-add-line"></i>
               <div data-i18n="Add Member">Add Member</div>
             </a>
           </li>

           <!-- Membership Types -->
           <li class="menu-item {{ request()->routeIs('membership-types.*') ? 'active' : '' }}">
             <a href="{{ route('membership-types.index') }}" class="menu-link">
               <i class="menu-icon icon-base ri ri-vip-crown-line"></i>
               <div data-i18n="Membership Types">Membership Types</div>
             </a>
           </li>

    <!-- Cashier -->
    <li class="menu-item {{ request()->routeIs('cashier.*') ? 'active' : '' }}">
      <a href="{{ route('cashier.index') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-bank-card-line"></i>
        <div data-i18n="Cashier">Cashier</div>
      </a>
    </li>

            <!-- Dining Management -->
        <li class="menu-item {{ request()->routeIs('dining.*') ? 'active' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ri ri-restaurant-line"></i>
            <div data-i18n="Dining Management">Dining Management</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('dining.index') ? 'active' : '' }}">
              <a href="{{ route('dining.index') }}" class="menu-link">
                <div data-i18n="Record Visits">Record Visits</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('dining.history*') ? 'active' : '' }}">
              <a href="{{ route('dining.history') }}" class="menu-link">
                <div data-i18n="Dining History">Dining History</div>
              </a>
            </li>
          </ul>
        </li>

    <!-- Discounts -->
    <li class="menu-item {{ request()->routeIs('discounts.*') ? 'active' : '' }}">
      <a href="{{ route('discounts.index') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-percent-line"></i>
        <div data-i18n="Discounts">Discounts</div>
      </a>
    </li>

    <!-- Reports -->
    <li class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ri ri-bar-chart-line"></i>
        <div data-i18n="Reports">Reports</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('reports.members') }}" class="menu-link">
            <div data-i18n="Member Reports">Member Reports</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('reports.dining') }}" class="menu-link">
            <div data-i18n="Dining Reports">Dining Reports</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('reports.discounts') }}" class="menu-link">
            <div data-i18n="Discount Reports">Discount Reports</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Notifications -->
    <li class="menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
      <a href="{{ route('notifications.index') }}" class="menu-link">
        <i class="menu-icon icon-base ri ri-mail-line"></i>
        <div data-i18n="Notifications">Notifications</div>
      </a>
    </li>

    <!-- Hotel Management -->
    <li class="menu-item {{ request()->routeIs('hotel.*') ? 'active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ri ri-building-line"></i>
        <div data-i18n="Hotel Management">Hotel Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('hotel.profile') }}" class="menu-link">
            <div data-i18n="Hotel Profile">Hotel Profile</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('hotel.account') }}" class="menu-link">
            <div data-i18n="Account Settings">Account Settings</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- User Profile -->
    <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ri ri-user-line"></i>
        <div data-i18n="My Profile">My Profile</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('users.profile') }}" class="menu-link">
            <div data-i18n="Profile">Profile</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('users.change-password') }}" class="menu-link">
            <div data-i18n="Change Password">Change Password</div>
          </a>
        </li>
      </ul>
    </li>

    @stack('sidebar-menu')
  </ul>
</aside>
<!-- / Menu --> 