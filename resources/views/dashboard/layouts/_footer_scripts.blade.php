  <!-- BEGIN VENDOR JS-->
  <script src="{{ asset('assets/admin/') }}/vendors/js/vendors.min.js" type="text/javascript"></script>
  <!-- BEGIN VENDOR JS-->
  <!-- BEGIN PAGE VENDOR JS-->
  <script src="{{ asset('assets/admin/') }}/vendors/js/charts/chartist.min.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/vendors/js/charts/chartist-plugin-tooltip.min.js"
  type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/vendors/js/charts/raphael-min.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/vendors/js/charts/morris.min.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/vendors/js/timeline/horizontal-timeline.js" type="text/javascript"></script>
  <!-- END PAGE VENDOR JS-->
  <!-- BEGIN MODERN JS-->
  <script src="{{ asset('assets/admin/') }}/js/core/app-menu.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/js/core/app.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/js/scripts/customizer.js" type="text/javascript"></script>
  <!-- END MODERN JS-->
  <!-- BEGIN PAGE LEVEL JS-->
  <script src="{{ asset('assets/admin/') }}/js/scripts/pages/dashboard-ecommerce.js" type="text/javascript"></script>
  @yield('js')
  <!-- END PAGE LEVEL JS-->
  @toastifyJs
