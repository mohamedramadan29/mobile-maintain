@extends('dashboard.layouts.app')
@section('title', 'الاعدادات ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/custom.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/css-rtl/plugins/forms/checkboxes-radios.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/editors/summernote.css">
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> الاعدادات </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.setting.index') }}"> الاعدادات </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> الاعدادات </a>
                                </li>
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
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger">{{ $error }}</div>
                                    @endforeach
                                @endif
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> الاعدادات </h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST" id="invoice-form"
                                            action="{{ route('dashboard.setting.update') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <!-- باقي الحقول -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone1"> رقم هاتف قسم الصيانة <span
                                                                    class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="phone1"
                                                                class="form-control" placeholder="" name="phone1"
                                                                value="{{ $setting->phone1 }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone2"> رقم هاتف الادارة <span
                                                                    class="required_span">
                                                                    *</span> </label>
                                                            <input required type="text" id="phone2"
                                                                class="form-control" placeholder="" name="phone2"
                                                                value="{{ $setting->phone2 }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="terms"> الشروط والاحكام <span
                                                                    class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <textarea required type="text" id="terms" class="form-control" placeholder="" name="terms"
                                                                >{{ $setting->terms }}</textarea>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> حفظ
                                                </button>
                                                <button type="button" class="mr-1 btn btn-warning">
                                                    <i class="ft-x"></i> رجوع
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/editors/summernote/summernote.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{ asset('assets/admin/') }}/js/scripts/editors/editor-summernote.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS-->
    <script>
        $(document).ready(function() {
            $('#terms').summernote({
                height: 300,
            });
        });
    </script>
@endsection
