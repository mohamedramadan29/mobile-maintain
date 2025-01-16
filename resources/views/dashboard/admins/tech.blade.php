@extends('dashboard.layouts.app')
@section('title', 'الفنين')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة الفنين </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> ادارة الفنين
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content-header-right col-md-6 col-12">

                </div>
            </div>
            <div class="content-body">

                <!-- Bordered striped start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> الاسم </th>
                                                    <th> عدد الاجهزة المسموح بها </th>
                                                    <th> الاصناف المتاحة </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($admins as $admin)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td> {{ $admin->name }} </td>
                                                        <td>
                                                            
                                                            {{ $admin->device_nums }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $admin_problems = json_decode($admin->problems);
                                                            @endphp
                                                            @if (!empty($admin_problems) && is_array($admin_problems) && count($admin_problems) > 0)
                                                                @foreach ($admin_problems as $admin_problem)
                                                                    <span class="badge badge-danger"> {{ $admin_problem }}
                                                                    </span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted"> لا يوجد صلاحيات  </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#update_tech_{{ $admin->id }}">
                                                                تعديل الصلاحيات <i class="la la-edit"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @include('dashboard.admins.update_tech')
                                                @empty
                                                    <td colspan="4"> لا يوجد بيانات </td>
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bordered striped end -->
            </div>
        </div>
    </div>


@endsection
