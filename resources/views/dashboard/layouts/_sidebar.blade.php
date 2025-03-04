  <!-- ////////////////////////////////////////////////////////////////////////////-->
  <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
      <div class="main-menu-content">
          <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
              <li class="nav-item {{ Route::is('dashboard.welcome') ? 'active' : '' }}"><a
                      href="{{ route('dashboard.welcome') }}"><i class="la la-home"></i><span class="menu-title"
                          data-i18n="nav.dash.main">الرئيسية</span></a>
              </li>
              @can('invoices')
                  <li class="nav-item {{ Route::is('dashboard.invoices.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-file-text"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                              الفواتير
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.invoices.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.invoices.index') }}"
                                  data-i18n="nav.users.user_profile"> جميع الفواتير
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.invoices.invoice-haif-time') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.invoices.invoice-haif-time') }}"
                                  data-i18n="nav.users.user_profile"> فواتير مر عليها نصف الوقت او اكثر
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.invoices.create') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.invoices.create') }}"
                                  data-i18n="nav.users.user_profile"> اضافة فاتورة
                              </a>
                          </li>
                      </ul>
                  </li>
              @endcan
              @can('roles')
                  <li class="nav-item {{ Route::is('dashboard.roles.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-television"></i><span class="menu-title" data-i18n="nav.role.main"> الصلاحيات
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.roles.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.roles.index') }}" data-i18n="nav.role.index">
                                  جميع الصلاحيات </a>
                          </li>
                          <li class="{{ Route::is('dashboard.roles.create') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.roles.create') }}"
                                  data-i18n="nav.templates.vert.classic_menu"> <i class="la la-plus"></i> <span
                                      class="menu-title""> اضافة صلاحية </a>
                          </li>
                      </ul>
                  </li>
              @endcan

              @can('admins')
                  <li class="nav-item {{ Route::is('dashboard.messages.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-television"></i><span class="menu-title" data-i18n="nav.role.main"> ادارة الرسائل
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.roles.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.messages.index') }}"
                                  data-i18n="nav.role.index">
                                  جميع الرسائل </a>
                          </li>
                      </ul>
                  </li>
              @endcan

              @can('admins')
                  <li class="nav-item{{ Route::is('dashboard.admins.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-user"></i><span class="menu-title" data-i18n="nav.users.main"> الموظفين
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.admins.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.admins.index') }}"
                                  data-i18n="nav.users.user_profile"> الموظفين
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.admins.tech') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.admins.tech') }}"
                                  data-i18n="nav.users.user_profile"> موظفين الصيانة
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.admins.create') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.admins.create') }}"
                                  data-i18n="nav.users.user_cards"> اضافة موظف </a>
                          </li>
                      </ul>
                  </li>
              @endcan

              @can('problem_categories')
                  <li class="nav-item {{ Route::is('dashboard.problem_categories.*') ? 'active' : '' }}"><a
                          href="#"><i class="la la-puzzle-piece"></i><span class="menu-title"
                              data-i18n="nav.users.main"> ادارة اقسام الاعطال
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.problem_categories.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.problem_categories.index') }}"
                                  data-i18n="nav.users.user_profile"> اقسام الاعطال
                              </a>
                          </li>

                      </ul>
                  </li>
                  <li class="nav-item {{ Route::is('dashboard.check_text.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-puzzle-piece"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                              اساسيات الفحص
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.check_text.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.check_text.index') }}"
                                  data-i18n="nav.users.user_profile"> اساسيات الفحص
                              </a>
                          </li>

                      </ul>
                  </li>
                  <li class="nav-item {{ Route::is('dashboard.speed_device.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-puzzle-piece"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                              جهاز سريع
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.speed_device.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.speed_device.index') }}"
                                  data-i18n="nav.users.user_profile"> جهاز سريع
                              </a>
                          </li>
                      </ul>
                  </li>
                  <li class="nav-item {{ Route::is('dashboard.programe_device.*') ? 'active' : '' }}"><a
                          href="#"><i class="la la-puzzle-piece"></i><span class="menu-title"
                              data-i18n="nav.users.main"> ادارة
                              جهاز برمجة
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.programe_device.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.programe_device.index') }}"
                                  data-i18n="nav.users.user_profile"> جهاز برمجة
                              </a>
                          </li>

                      </ul>
                  </li>
              @endcan


              @can('tech_invoices')
                  <li class="nav-item {{ Route::is('dashboard.tech_invoices.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-file-text"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                              فواتيري
                          </span></a>
                      <ul class="menu-content">
                          <li class="{{ Route::is('dashboard.tech_invoices.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.tech_invoices.index') }}"
                                  data-i18n="nav.users.user_profile"> فواتيري
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.tech_invoices.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.tech_invoices.available') }}"
                                  data-i18n="nav.users.user_profile"> الفواتير المتاحة
                              </a>
                          </li>
                      </ul>
                  </li>
              @endcan

              @can('tech_invoices')
                  <li class="nav-item {{ Route::is('dashboard.invoices.*') ? 'active' : '' }}"><a href="#"><i
                              class="la la-file-text"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                              الفواتير
                          </span></a>
                      <ul class="menu-content">

                          <li class="{{ Route::is('dashboard.invoices.invoice-haif-time') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.invoices.invoice-haif-time') }}"
                                  data-i18n="nav.users.user_profile"> فواتير مر عليها نصف الوقت او اكثر
                              </a>
                          </li>

                      </ul>
                  </li>
              @endcan

              <li class="nav-item {{ Route::is('dashboard.update_profile.*') ? 'active' : '' }}"><a href="#"><i
                          class="la la-user"></i><span class="menu-title" data-i18n="nav.users.main"> ادارة
                          حسابي
                      </span></a>
                  <ul class="menu-content">
                      <li class="{{ Route::is('dashboard.update_profile') ? 'active' : '' }}">
                          <a class="menu-item" href="{{ route('dashboard.update_profile') }}"
                              data-i18n="nav.users.user_profile"> تعديل البيانات
                          </a>
                      </li>
                      <li class="{{ Route::is('dashboard.update_password') ? 'active' : '' }}">
                          <a class="menu-item" href="{{ route('dashboard.update_password') }}"
                              data-i18n="nav.users.user_profile"> تعديل كلمة المرور
                          </a>
                      </li>
                  </ul>
              </li>

          </ul>
      </div>
  </div>
