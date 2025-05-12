@extends('dashboard.layouts.app')
@section('title', 'تعديل القسم')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block">تعديل القسم</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.problem_categories.index') }}">الاعطال</a></li>
                                <li class="breadcrumb-item active">تعديل القسم</li>
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
                                    <h4 class="card-title">تعديل القسم: {{ $problem->name }}</h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.problem_categories.update', $problem->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label>الاسم</label>
                                                <input type="text" class="form-control" name="name" value="{{ $problem->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>عدد الدقائق للاصلاح</label>
                                                <input type="number" min="1" class="form-control" name="solved_time" value="{{ $problem->solved_time }}" required>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> تحديث
                                                </button>
                                                <a href="{{ route('dashboard.problem_categories.index') }}" class="btn btn-warning mr-1">
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
