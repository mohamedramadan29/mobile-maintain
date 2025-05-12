@extends('dashboard.layouts.app')
@section('title', 'اضافة فحص اضافي جديد')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block">اضافة فحص اضافي جديد</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.more_checks.index') }}">الفحوصات الاضافية</a></li>
                                <li class="breadcrumb-item active">اضافة فحص اضافي جديد</li>
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
                                    <h4 class="card-title">اضافة فحص اضافي جديد</h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form action="{{ route('dashboard.more_checks.create') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label class="required">الاسم</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                    name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check"></i> اضافة
                                                </button>
                                                <a href="{{ route('dashboard.more_checks.index') }}" class="btn btn-warning mr-1">
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
