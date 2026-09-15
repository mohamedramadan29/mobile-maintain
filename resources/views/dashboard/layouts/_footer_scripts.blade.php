  <!-- BEGIN VENDOR JS-->
  <script src="{{ asset('assets/admin/') }}/vendors/js/vendors.min.js" type="text/javascript"></script>
  <!-- BEGIN VENDOR JS-->
  {{-- حُذفت مكتبات الرسوم (chartist/raphael/morris) و dashboard-ecommerce.js:
     لا يوجد أي عنصر رسم بياني في التطبيق، وكانت ترمي أخطاء في كل صفحة --}}
  <!-- BEGIN PAGE VENDOR JS-->
  <script src="{{ asset('assets/admin/') }}/vendors/js/timeline/horizontal-timeline.js" type="text/javascript"></script>
  <!-- END PAGE VENDOR JS-->
  <!-- BEGIN MODERN JS-->
  <script src="{{ asset('assets/admin/') }}/js/core/app-menu.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/js/core/app.js" type="text/javascript"></script>
  <script src="{{ asset('assets/admin/') }}/js/scripts/customizer.js" type="text/javascript"></script>
  <!-- END MODERN JS-->
  <!-- BEGIN PAGE LEVEL JS-->
  @yield('js')
  <!-- END PAGE LEVEL JS-->
  @toastifyJs
