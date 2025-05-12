@extends('dashboard.layouts.app')
@section('title', 'الموظفين')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة الموظفين </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> ادارة الموظفين
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
                            <div class="card-header">
                                <a href="{{ route('dashboard.admins.create') }}" class="btn btn-primary"> اضافة موظف </a>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> الاسم </th>
                                                    <th> البريد الالكتروني </th>
                                                    <th> رقم الهاتف </th>
                                                    <th> نوع الموظف </th>
                                                    <th> الصلاحية </th>
                                                    <th> الحالة </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($admins as $admin)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td> {{ $admin->name }} </td>
                                                        <td>
                                                            {{ $admin->email }}
                                                        </td>
                                                        <td>
                                                            {{ $admin->phone }}
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-success"> {{ $admin->type }} </span>
                                                        </td>
                                                        <td>
                                                            {{ $admin->role->role }}
                                                        </td>
                                                        <td>
                                                            {{ $admin->status == 1 ? 'مفعل' : 'غير مفعل' }}
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-info btn-sm"
                                                                href="{{ route('dashboard.admins.update', $admin->id) }}"><i
                                                                    class="la la-edit"></i> تعديل </a>
                                                            <a href="{{ route('dashboard.admins.destroy', $admin->id) }}"
                                                                type="button" class="btn btn-danger btn-sm">
                                                                حذف <i class="la la-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>


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
