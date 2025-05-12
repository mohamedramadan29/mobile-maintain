@extends('dashboard.layouts.app')
@section('title', 'ادارة الصلاحيات ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة الصلاحيات </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> ادارة الصلاحيات
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
                                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-primary"> اضافة صلاحية </a>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> اسم الصلاحية </th>
                                                    <th> الصلاحيات </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($roles as $role)
                                                    <tr>
                                                        <th scope="row">{{ $role->iteration }}</th>
                                                        <td> {{ $role->role }} </td>
                                                        <td>
                                                            @foreach (json_decode($role->permission) as $permission)
                                                                @foreach (Config::get('permissions') as $key => $value)
                                                                    @if ($key == $permission)
                                                                        <span class="badge badge-info"> {{ $value }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-info btn-sm"
                                                                href="{{ route('dashboard.roles.update', $role->id) }}"><i
                                                                    class="la la-edit"></i> تعديل </a>
                                                            <a href="{{ route('dashboard.roles.destroy', $role->id) }}"
                                                                class="btn btn-danger btn-sm" >
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
