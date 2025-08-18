<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @if(auth()->user()->role !== 'superadmin' && auth()->user()->hotel && auth()->user()->hotel->logo_path)
          <img src="{{ auth()->user()->hotel->logo_url }}" alt="{{ auth()->user()->hotel->name }}" 
               style="width: 35px; height: 35px; object-fit: contain; border-radius: 6px;">
        @else
          <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
              <path d="M13.7918663,0.358365126 L3.39788168,7.44144159 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747186 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
              <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.7474144,6.10983573 13.1073746,4.65957225 10.6997674,3.63361125 C8.65485998,2.74983727 6.83482694,2.24581969 5.47320593,6.00457225 Z" id="path-3"></path>
              <path d="M7.50063644,21.2294429 L12.3234468,23.3556932 C14.0758029,24.2297078 14.6359495,25.5394451 13.7326044,27.0960096 C12.7014291,28.8997156 10.7660073,29.5483889 8.94622138,28.9589449 C7.30276361,28.4321542 5.32503817,27.7013091 4.01469323,26.5180557 C2.77059474,25.3246174 2.91447364,23.6164152 4.37897705,22.0198339 C4.93446909,21.4566135 6.28710187,21.0632871 7.50063644,21.2294429 Z" id="path-4"></path>
              <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17.0074193 24,17.0074193 C23.5672596,17.0074193 23.1461923,16.8596443 22.8,16.6 L18.3333333,12.1333333 C17.5652174,11.36125 16.4347826,11.36125 15.6666667,12.1333333 L11.2,16.6 C10.4347826,17.3586957 9.56521739,17.3586957 8.8,16.6 L4.33333333,12.1333333 C3.56521739,11.36125 2.43478261,11.36125 1.66666667,12.1333333 L1.4,12.4 C0.637681159,13.1524734 0.637681159,14.3475266 1.4,15.1 L6.4,21.7666667 C7.16231884,22.5191401 8.35747313,22.5191401 9.12,21.7666667 L13.6,17.3 C14.3623188,16.5475266 15.5574731,16.5475266 16.32,17.3 L20.8,21.7666667 C21.5623188,22.5191401 22.7574731,22.5191401 23.52,21.7666667 L28.52,15.1 C29.2823188,14.3475266 29.2823188,13.1524734 28.52,12.4 L28.26,12.1333333 C27.4918841,11.36125 26.3611159,11.36125 25.6,12.1333333 L20.6,7.13333333 Z" id="path-5"></path>
            </defs>
            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g id="Brand-Logo" transform="translate(27.000000, 27.000000)">
                <g id="Icon" transform="translate(0.000000, 27.000000)">
                  <use fill="#696cff" xlink:href="#path-1"></use>
                  <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-1"></use>
                </g>
                <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                  <use fill="#696cff" xlink:href="#path-3"></use>
                  <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                </g>
                <g id="Rectangle" transform="translate(0.000000, 19.000000)">
                  <use fill="#696cff" xlink:href="#path-4"></use>
                  <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                </g>
                <polygon id="Shape" opacity="0.077783" fill="#000000" points="3.26129167 27.8862917 14.8872917 4.00691667 26.4132917 27.8862917"></polygon>
                <polygon id="Shape" opacity="0.077783" fill="#000000" points="0 19.52875 8.41666667 38.6875 16.8333333 19.52875"></polygon>
                <polygon id="Shape" opacity="0.077783" fill="#000000" points="9.57552083 4.60625 17.9963542 23.5208333 26.4171875 4.60625"></polygon>
              </g>
            </g>
          </svg>
        @endif
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">
        @if(auth()->user()->role === 'superadmin')
          {{ __('app.system_admin') }}
        @else
          {{ auth()->user()->hotel->name ?? 'Restaurant MS' }}
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