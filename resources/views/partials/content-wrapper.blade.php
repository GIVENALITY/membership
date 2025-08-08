<!-- Layout container -->
<div class="layout-page">
  @include('partials.navbar')

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      @yield('content')
    </div>
    <!-- / Content -->

    @include('partials.footer')

    <div class="content-backdrop fade"></div>
  </div>
  <!-- Content wrapper -->
</div>
<!-- / Layout page --> 