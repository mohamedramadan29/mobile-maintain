@extends('dashboard.layouts.app')
@section('title', 'حذف قسم مشكلة السرعة')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block">حذف قسم مشكلة السرعة</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.speed_problem_categories.index') }}">اقسام مشاكل السرعة</a></li>
                                <li class="breadcrumb-item active">حذف قسم مشكلة السرعة</li>
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
                                <div class="card-header bg-danger">
                                    <h4 class="card-title text-white">تأكيد حذف قسم مشكلة السرعة</h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.speed_problem_categories.destroy', $problem->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <h5 class="text-danger mb-2">
                                                    <i class="la la-exclamation-triangle"></i> هل انت متاكد من حذف قسم مشكلة السرعة ؟
                                                </h5>
                                                <div class="form-group">
                                                    <label>الاسم</label>
                                                    <input type="text" class="form-control bg-light" value="{{ $problem->name }}" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>وقت الحل</label>
                                                    <input type="text" class="form-control bg-light" value="{{ $problem->solved_time }} دقيقة" disabled>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="la la-trash"></i> تأكيد الحذف
                                                </button>
                                                <a href="{{ route('dashboard.speed_problem_categories.index') }}" class="btn btn-light mr-1">
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
