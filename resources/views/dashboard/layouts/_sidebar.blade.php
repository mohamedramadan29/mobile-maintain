  <!-- ////////////////////////////////////////////////////////////////////////////-->
  <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow " data-scroll-to-active="true">
      <div class="main-menu-content">
          <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
              <li class="nav-item {{ Route::is('dashboard.welcome') ? 'active' : '' }}"><a
                      href="{{ route('dashboard.welcome') }}"><i class="la la-home"></i><span class="menu-title"
                          data-i18n="nav.dash.main">الرئيسية</span></a>

              </li>
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
              @endcan

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
                          <li class="{{ Route::is('dashboard.invoices.create') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.invoices.create') }}"
                                  data-i18n="nav.users.user_profile"> اضافة فاتورة
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
                          <li class="{{ Route::is('da.tech_invoices.index') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.tech_invoices.index') }}"
                                  data-i18n="nav.users.user_profile"> فواتيري
                              </a>
                          </li>
                          <li class="{{ Route::is('dashboard.invoices.create') ? 'active' : '' }}">
                              <a class="menu-item" href="{{ route('dashboard.tech_invoices.available') }}"
                                  data-i18n="nav.users.user_profile"> الفواتير المتاحة
                              </a>
                          </li>
                      </ul>
                  </li>
              @endcan

              <li class="nav-item {{ Route::is('dashboard.tech_invoices.*') ? 'active' : '' }}"><a href="#"><i
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
                              data-i18n="nav.users.user_profile">  تعديل كلمة المرور
                          </a>
                      </li>
                  </ul>
              </li>

          </ul>
      </div>
  </div>
