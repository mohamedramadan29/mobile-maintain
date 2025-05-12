@extends('dashboard.layouts.app')
@section('title', 'تعديل صلاحيات الفني')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block">تعديل صلاحيات الفني</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.admins.tech') }}">الفنيين</a></li>
                                <li class="breadcrumb-item active">تعديل صلاحيات الفني</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">تعديل صلاحيات الفني: {{ $admin->name }}</h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.admins.update_tech', $admin->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label>عدد الاجهزة المسموح بها في نفس الوقت</label>
                                                <input type="number" min='1' max="10" class="form-control" name="device_nums"
                                                    value="{{ $admin->device_nums }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="title">الاعطال المتاحة للفني</label>
                                                <div class="skin skin-square">
                                                    <div class="col-md-12 col-sm-12 d-flex flex-column">
                                                        @php
                                                            $admin_problems = json_decode($admin->problems, true) ?: [];
                                                        @endphp
                                                        <div class="mb-3">
                                                            <h5>صلاحيات اعطال فحص شامل</h5>
                                                            @foreach ($problems as $problem)
                                                                <fieldset class="mb-1">
                                                                    <input {{ in_array($problem->name, $admin_problems) ? 'checked' : '' }}
                                                                        type="checkbox" id="input-{{ $problem->id }}" name="problems[]"
                                                                        value="{{ $problem->name }}">
                                                                    <label for="input-{{ $problem->id }}">
                                                                        {{ $problem->name }}
                                                                    </label>
                                                                </fieldset>
                                                            @endforeach
                                                        </div>

                                                        <div class="mb-3">
                                                            <h5>صلاحيات اعطال جهاز برمجة</h5>
                                                            @foreach ($programe_problems as $programe_problem)
                                                                <fieldset class="mb-1">
                                                                    <input {{ in_array($programe_problem->name, $admin_problems) ? 'checked' : '' }}
                                                                        type="checkbox" id="inputprograme-{{ $programe_problem->id }}"
                                                                        name="problems[]" value="{{ $programe_problem->name }}">
                                                                    <label for="inputprograme-{{ $programe_problem->id }}">
                                                                        {{ $programe_problem->name }}
                                                                    </label>
                                                                </fieldset>
                                                            @endforeach
                                                        </div>

                                                        <div class="mb-3">
                                                            <h5>صلاحيات اعطال جهاز سريع</h5>
                                                            @foreach ($speed_problems as $speed_problem)
                                                                <fieldset class="mb-1">
                                                                    <input {{ in_array($speed_problem->name, $admin_problems) ? 'checked' : '' }}
                                                                        type="checkbox" id="inputspeed-{{ $speed_problem->id }}" name="problems[]"
                                                                        value="{{ $speed_problem->name }}">
                                                                    <label for="inputspeed-{{ $speed_problem->id }}">
                                                                        {{ $speed_problem->name }}
                                                                    </label>
                                                                </fieldset>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> تحديث
                                                </button>
                                                <a href="{{ route('dashboard.admins.tech') }}" class="btn btn-warning mr-1">
                                                    <i class="la la-times"></i> الغاء
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
