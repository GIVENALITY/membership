@include('partials.header')

@include('partials.sidebar')

@include('partials.content-wrapper')

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
<!-- / Layout wrapper -->

<div class="buy-now">
  <a
    href="https://themeselection.com/item/materio-dashboard-pro-bootstrap/"
    target="_blank"
    class="btn btn-danger btn-buy-now"
    >Upgrade to Pro</a
  >
</div>

<!-- Core JS -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
@stack('page-js')

<!-- Place this tag before closing body tag for github widget button. -->
<script async="async" defer="defer" src="https://buttons.github.io/buttons.js"></script>

@stack('scripts')
</body>
</html> 